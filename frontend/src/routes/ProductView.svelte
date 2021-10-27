<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import MajorButton from "../components/MajorButton.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { loginToken } from "../stores";

  export let id: string;
  export let location: string;

  type Item = Model.IProduct;

  let token = "";
  let isEditing = false;
  let isDeletingPending = false;
  let isSubmitingPending = false;
  let payload: Item;
  let changeMap = new Map<keyof Item, string>();

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch(`${FETCH_ROOT}/api/products/${id}`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });

    payload = (await res.json())[0];

    console.log(payload);
  };

  start();

  const handleEdit = async () => {
    isEditing = !isEditing;

    // is in submitting mode
    if (isEditing || isSubmitingPending || !changeMap.size) {
      return;
    }

    isSubmitingPending = true;

    try {
      // transform map
      const body = new URLSearchParams();

      changeMap.forEach((v, k) => {
        body.append(k, v);
      });

      // send request
      const res = await fetch(`${FETCH_ROOT}/api/products/${id}`, {
        method: "put",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        }),
        body
      });

      // handle error
      if (res.status === 200) {
        changeMap.clear();
      }
      else {
        throw new Error("the status is not 200");
      }
    }
    catch (err) {
      console.error(err);
    }
    finally {
      isSubmitingPending = false;
    }
  };

  const handleDelete = async () => {
    if (isDeletingPending) {
      return;
    }

    isDeletingPending = true;

    try {
      // send request
      const res = await fetch(`${FETCH_ROOT}/api/products/${id}`, {
        method: "delete",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        })
      });

      // handle error
      if (res.status === 200) {
        navigate(`/products`, { replace: true });
      }
      else {
        throw new Error(res.statusText);
      }
    }
    catch (err) {
      console.error(err);
    }
    finally {
      isDeletingPending = false;
    }
  };

  const handleInputChange = <T extends keyof Item>(channel: T) => {
    return (e: { currentTarget: EventTarget & HTMLInputElement | HTMLSelectElement }) => {
      changeMap.set(channel, e.currentTarget.value);
    };
  };
</script>

<template>
  <main>
    {#if payload}
      <div class="layout">
        <div class="two-grid">
          <aside>
            <div class="profile-img">
              <img src="../images/person-placeholder.png" alt="person" />
            </div>
          </aside>
          <aside class="info-grid">
            <section class="info-header">
              <div class="id">#{payload.productCode}</div>
              <div>
                <span class="name">{payload.productName}</span>
              </div>
            </section>
            <section class="form-information">
              <aside>Product Name:</aside>
              <input
                type="text"
                value={payload.productName}
                disabled={!isEditing}
                required
                on:change={handleInputChange("productName")}
              >
              <aside>Product Line:</aside>
              <input
                type="text"
                value={payload.productLine}
                disabled={!isEditing}
                required
                on:change={handleInputChange("productLine")}
              >
              <aside>Product Scale:</aside>
              <input
                type="text"
                value={payload.productScale}
                disabled={!isEditing}
                required
                on:change={handleInputChange("productScale")}
              >
              <aside>Product Vendor:</aside>
              <input
                type="text"
                value={payload.productVendor}
                disabled={!isEditing}
                required
                on:change={handleInputChange("productVendor")}
              >
              <aside>Product Description:</aside>
              <input
                type="text"
                value={payload.productDescription}
                disabled={!isEditing}
                required
                on:change={handleInputChange("productDescription")}
              >
              <aside>Quantity In Stock:</aside>
              <input
                type="number"
                value={payload.quantityInStock}
                disabled={!isEditing}
                required
                on:change={handleInputChange("quantityInStock")}
              >
              <aside>Buy Price:</aside>
              <input
                type="number"
                value={payload.buyPrice}
                disabled={!isEditing}
                required
                on:change={handleInputChange("buyPrice")}
              >
              <aside>MSRP:</aside>
              <input
                type="number"
                value={payload.MSRP}
                disabled={!isEditing}
                required
                on:change={handleInputChange("MSRP")}
              >
            </section>
            <section class="danger-zone">
              <div class="danger-label">Danger Zone</div>
              <MajorButton
                width="200px"
                label={isEditing ? "Submit" : "Edit"}
                isPending={isSubmitingPending}
                on:click={handleEdit}
              />
              <MajorButton
                width="200px"
                label={"Delete"}
                isPending={isDeletingPending}
                on:click={handleDelete}
              />
            </section>
          </aside>
        </div>
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
    padding-top: 2em;
  }

  .two-grid {
    display: grid;
    grid-template-columns: 224px 1fr;
    gap: 2em;
  }

  .profile-img {
    height: 224px;
    background-color: #E1DEDE;
    border-radius: 8px;
    overflow: hidden;

    > img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  .info-grid {
    display: grid;
    gap: 1em;
  }

  .info-header {
    .id {
      color: lightgray;
      font-size: smaller;
    }

    .name {
      font-weight: bold;
      font-size: xx-large;
    }
  }

  .form-information {
    display: grid;
    grid-template-columns: 148px 1fr;
    gap: 8px;
    align-items: center;

    > :nth-child(odd) {
      font-weight: bold;
    }
  }

  .danger-zone {
    display: grid;
    gap: 8px;

    .danger-label {
      color: #AB1A1A;
      font-weight: bold;
      border-bottom: 1px solid lightgrey;
    }
  }
</style>