<script lang="ts">
  import { navigate, Link } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import MajorButton from "../components/MajorButton.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { loginToken } from "../stores";

  export let id: string;
  export let location: string;

  type Item = Model.IPayment;

  let token = "";
  let isEditing = false;
  let isDeletingPending = false;
  let isSubmitingPending = false;
  let mainPayload: Item;
  let customerPayload: Model.ICustomer;
  let changeMap = new Map<keyof Item, string>();

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch(`${FETCH_ROOT}/api/payments/${id}`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });

    const tempPayload: Item = (await res.json())[0];

    const res2 = await fetch(`${FETCH_ROOT}/api/customers/${tempPayload.customerNumber}`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });

    customerPayload = (await res2.json())[0];
    mainPayload = tempPayload

    console.log(mainPayload);
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
      const res = await fetch(`${FETCH_ROOT}/api/payments/${id}`, {
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
      const res = await fetch(`${FETCH_ROOT}/api/payments/${id}`, {
        method: "delete",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        })
      });

      // handle error
      if (res.status === 200) {
        navigate(`/payments`, { replace: true });
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
    {#if mainPayload}
      <div class="layout">
        <div class="two-grid">
          <aside>
            <div class="profile-img">
              <img src="../images/person-placeholder.png" alt="person" />
            </div>
          </aside>
          <aside class="info-grid">
            <section class="info-header">
              <div class="id">#{id}</div>
              <div>
                <Link to={`/customers/${customerPayload.customerNumber}`}>
                  <span class="name">{customerPayload.customerName}</span>
                </Link>
              </div>
            </section>
            <section class="form-information">
              <aside>Cheque Number:</aside>
              <input
                type="text"
                value={mainPayload.checkNumber}
                disabled
              >
              <aside>Payment Date:</aside>
              <input
                type="date"
                value={mainPayload.paymentDate}
                disabled={!isEditing}
                required
                on:change={handleInputChange("paymentDate")}
              >
              <aside>Amount:</aside>
              <input
                type="text"
                value={mainPayload.amount}
                disabled={!isEditing}
                required
                on:change={handleInputChange("amount")}
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