<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import MajorButton from "../components/MajorButton.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { loginToken } from "../stores";

  export let location: string;

  let token = "";
  let list: null | Model.IProduct[] = null;
  let isInAddingMode = false;
  let isAddingPending = false;
  let form: HTMLFormElement;
  let errMsg = "";

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch(`${FETCH_ROOT}/api/products`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });
    list = await res.json();

    console.log(list);
  };

  start();

  const swapCreateMode = () => {
    isInAddingMode = !isInAddingMode;
    errMsg = "";
  };

  const submitAdding = async () => {
    if (isAddingPending) {
      return;
    }

    isAddingPending = true;
    errMsg = "";

    try {
      // send request
      const res = await fetch(`${FETCH_ROOT}/api/products`, {
        method: "post",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        }),
        body: new FormData(form)
      });

      const data = await res.json();

      // handle request
      if (data.errors) {
        throw new Error(data.errors);
      }
      else {
        form.reset();
      }
    }
    catch (err) {
      errMsg = `${err}`;
      console.log(err);
    }
    finally {
      isAddingPending = false;
    }
  };
</script>

<template>
  <main>
    {#if list}
      <div class="layout">
        <div class="create-btn-container">
          <MajorButton
            width="200px"
            label={isInAddingMode ? "View Products" : "Add A Product"}
            on:click={swapCreateMode}
          />
        </div>
        {#if isInAddingMode}
        <form class="create-form" bind:this={form} on:submit|preventDefault={submitAdding}>
          <div class="form-half-grid">
            <aside>
              <label for="productCode">Product Code</label>
              <input id="productCode" type="text" name="productCode" minlength="8" maxlength="50" disabled={isAddingPending} required />
            </aside>
            <aside>
              <label for="productName">Product Name</label>
              <input id="productName" type="text" name="productName" maxlength="50" disabled={isAddingPending} required />
            </aside>
          </div>
          <div class="form-half-grid">
            <aside>
              <label for="productLine">Product Line</label>
              <input id="productLine" type="text" name="productLine" maxlength="50" disabled={isAddingPending} required />
            </aside>
            <aside>
              <label for="productScale">Product Scale</label>
              <input id="productScale" type="text" name="productScale" maxlength="50" disabled={isAddingPending} required />
            </aside>
          </div>
          <div class="form-half-grid">
            <aside>
              <label for="productVendor">Product Vendor</label>
              <input id="productVendor" type="text" name="productVendor" maxlength="50" disabled={isAddingPending} required />
            </aside>
            <aside>
              <label for="productDescription">Product Description</label>
              <input id="productDescription" type="text" name="productDescription" maxlength="50" disabled={isAddingPending} required />
            </aside>
          </div>
          <div class="form-half-grid">
            <aside>
              <label for="quantityInStock">Quantity In Stock</label>
              <input id="quantityInStock" type="number" name="quantityInStock" min="0" disabled={isAddingPending} required />
            </aside>
            <aside>
              <label for="buyPrice">Buy Price</label>
              <input id="buyPrice" type="number" name="buyPrice" min="0" disabled={isAddingPending} required />
            </aside>
          </div>
          <aside>
            <label for="MSRP">MSRP</label>
            <small>Manufacturer's Suggested Retail Price</small>
            <input id="MSRP" type="number" name="MSRP" min="0" disabled={isAddingPending} required />
          </aside>
          <div class="form-btn-container">
            <MajorButton
              width="200px"
              type="submit"
              label="Submit"
              isPending={isAddingPending}
            />
          </div>
          {#if errMsg}
            <div class="err-msg">{errMsg}</div>
          {/if}
        </form>
        {:else}
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Full Name</th>
                  <th>Phone</th>
                  <th>City</th>
                  <th>State</th>
                  <th>Country</th>
                </tr>
              </thead>
              <tbody>
                {#each list as el, i}
                  <tr on:click={() => navigate(`/products/${el.productCode}`)}>
                    <td>{i + 1}</td>
                    <td>{el.productName}</td>
                    <td>{el.productDescription}</td>
                    <td>{el.productLine}</td>
                    <td>{el.productVendor}</td>
                    <td>{el.buyPrice}
                      <div on:click|stopPropagation class="row-option">
                        <i class="fas fa-clipboard"></i>
                      </div>
                    </td>
                  </tr>
                {/each}
              </tbody>
            </table>
          </div>
        {/if}
      </div>
    {:else}
      <FullWaiter />
    {/if}
  </main>
</template>

<style lang="scss">
  .layout {
    max-width: 900px;
    margin: 0 auto;
  }

  .create-btn-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 1em;
  }

  .form-btn-container {
    display: flex;
    margin-top: 1em;
    justify-content: center;
  }

  .create-form {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;

    input, select {
      width: 100%;
      padding: 6.4px 12px;

      &[required]:empty {
        border-color: darkorange;
      }
    }

    select {
      height: 38.78px;
      border-radius: 0;
      margin: 0;
      &:focus {
        outline: 0;
      }
    }

    .form-half-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }
  }

  .table-container {
    margin-top: 1em;
  }

  .err-msg {
    color: #AB1A1A;
    font-size: small;
    text-align: center;
  }
</style>