<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
import MajorButton from "../components/MajorButton.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { loginToken } from "../stores";

  export let location: string;

  let token = "";
  let list: null | Model.ICustomer[] = null;
  let isInAddingMode = false;
  let isAddingPending = false;
  let form: HTMLFormElement;
  let errMsg = "";

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch(`${FETCH_ROOT}/api/customers`, {
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
      const res = await fetch(`${FETCH_ROOT}/api/customers`, {
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
            label={isInAddingMode ? "View Customers" : "Add A Customer"}
            on:click={swapCreateMode}
          />
        </div>
        {#if isInAddingMode}
          <form class="create-form" bind:this={form} on:submit|preventDefault={submitAdding}>
            <aside>
              <label for="customerName">Customer Name</label>
              <input id="customerName" type="text" name="customerName" maxlength="50" disabled={isAddingPending} required />
            </aside>
            <div class="form-half-grid">
              <aside>
                <label for="contactFirstName">Contact First Name</label>
                <input id="contactFirstName" type="text" name="contactFirstName" maxlength="50" disabled={isAddingPending} required />
              </aside>
              <aside>
                <label for="contactLastName">Contact Last Name</label>
                <input id="contactLastName" type="text" name="contactLastName" maxlength="50" disabled={isAddingPending} required />
              </aside>
            </div>
            <aside>
              <label for="phone">Phone</label>
              <input id="phone" type="text" name="phone" maxlength="50" disabled={isAddingPending} required />
            </aside>
            <div class="form-half-grid">
              <aside>
                <label for="addressLine1">Primary Address Line</label>
                <input id="addressLine1" type="text" name="addressLine1" maxlength="50" disabled={isAddingPending} required />
              </aside>
              <aside>
                <label for="addressLine2">Secondary Address Line</label>
                <input id="addressLine2" type="text" name="addressLine2" maxlength="50" disabled={isAddingPending} />
              </aside>
            </div>
            <div class="form-half-grid">
              <aside>
                <label for="city">City</label>
                <input id="city" type="text" name="city" maxlength="50" disabled={isAddingPending} required />
              </aside>
              <aside>
                <label for="state">State</label>
                <input id="state" type="text" name="state" maxlength="50" disabled={isAddingPending} />
              </aside>
            </div>
            <div class="form-half-grid">
              <aside>
                <label for="postalCode">Postal Code</label>
                <input id="postalCode" type="text" name="postalCode" maxlength="50" disabled={isAddingPending} />
              </aside>
              <aside>
                <label for="country">Country</label>
                <input id="country" type="text" name="country" maxlength="50" disabled={isAddingPending} required />
              </aside>
            </div>
            <div class="form-half-grid">
              <aside>
                <label for="salesRepEmployeeNumber">Personal Sales Number</label>
                <input id="salesRepEmployeeNumber" type="number" name="salesRepEmployeeNumber" disabled={isAddingPending} />
              </aside>
              <aside>
                <label for="creditLimit">Credit Limit</label>
                <input id="creditLimit" type="number" name="creditLimit" disabled={isAddingPending} min="0" />
              </aside>
            </div>
            <aside>
              <label for="memberPoint">Member Point</label>
              <input id="memberPoint" type="number" name="memberPoint" disabled={isAddingPending} required />
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
                  <tr on:click={() => navigate(`/customers/${el.customerNumber}`)}>
                    <td>{i + 1}</td>
                    <td>{el.customerName}</td>
                    <td>{el.phone}</td>
                    <td>{el.city}</td>
                    <td>{el.state || ""}</td>
                    <td>{el.country}
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
