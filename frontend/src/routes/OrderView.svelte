<script lang="ts">
  import { navigate, Link } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import MajorButton from "../components/MajorButton.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { loginToken } from "../stores";

  export let id: string;
  export let location: string;

  type Item = Model.IOrder;

  let token = "";
  let isEditing = false;
  let isDeletingPending = false;
  let isSubmitingPending = false;
  let mainPayload: Item[];
  let customerPayload: Model.ICustomer;
  let changeMap = new Map<keyof Item, string>();

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const resOrder = await fetch(`${FETCH_ROOT}/api/orders/${id}`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });

    const tempPayload: Item[] = await resOrder.json();

    const resCustomer = await fetch(`${FETCH_ROOT}/api/customers/${tempPayload[0].customerNumber}`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });

    customerPayload = (await resCustomer.json())[0];
    mainPayload = tempPayload;

    console.log(mainPayload);
  };

  start();

  const getCustomerAddress = (customer: Model.ICustomer) => {
    let address = customer.addressLine1;
    address += `, ${customer.city}`;
    address += customer.state ? `, ${customer.state}` : "";
    address += `, ${customer.country}`;
    address += ` ${customer.postalCode}`;

    return address;
  };

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
      const res = await fetch(`${FETCH_ROOT}/api/orders/${id}`, {
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
        const f = await res.json();
        console.log(f);
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
      const res = await fetch(`${FETCH_ROOT}/api/orders/${id}`, {
        method: "delete",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        })
      });

      // handle error
      if (res.status === 200) {
        navigate(`/orders`, { replace: true });
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
        <section class="customer-detail-container">
          <div class="id">#{id}</div>
          <div class="customer-name">
            <Link to={`/customers/${customerPayload.customerNumber}`}>
              <span>{customerPayload.customerName}</span>
            </Link>
          </div>
          <div class="customer-address">
            <div>
              <i class="fas fa-map-marker-alt"></i>
              <span>{getCustomerAddress(customerPayload)}</span>
            </div>
            <div>
              <i class="fas fa-phone-alt"></i>
              <span>{customerPayload.phone}</span>
            </div>
          </div>
        </section>
        <section class="table-container">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Product Id.</th>
                <th>Discount Code</th>
                <th>Quantity</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              {#each mainPayload as item, i}
                <tr>
                  <td>{i + 1}</td>
                  <td>{item.productCode}</td>
                  <td>{item.discountCode ?? ""}</td>
                  <td>{item.quantityOrdered}</td>
                  <td>${(item.quantityOrdered * Number.parseFloat(item.priceEach)).toFixed(2)}
                    <div on:click|stopPropagation class="row-option">
                      <i class="fas fa-clipboard"></i>
                    </div>
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        </section>
        <section class="comment-section">
          <label for=""><b>Comment</b></label>
          <textarea name="" id="" cols="30" rows="10" bind:value={mainPayload[0].comments} disabled></textarea>
        </section>
        <section class="form-information">
          <aside>Status:</aside>
          <select
            value={mainPayload[0].status}
            disabled={!isEditing}
            required
            on:change={handleInputChange("status")}
          >
            <option value="canceled">Canceled</option>
            <option value="disputed">Disputed</option>
            <option value="in process">In Process</option>
            <option value="on hold">On Hold</option>
            <option value="resolved">Resolved</option>
            <option value="shipped">Shipped</option>
            <option value="preorder" disabled>Pre-Order</option>
          </select>
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
      </div>
    {:else}
      <FullWaiter />
    {/if}
  </main>
</template>

<style lang="scss">
  .layout {
    display: grid;
    gap: 1em;
    max-width: 900px;
    margin: 0 auto;
    padding-top: 2em;
  }

  .id {
    color: lightgray;
    font-size: smaller;
  }

  .customer-name {
    font-size: xx-large;
    font-weight: bold;
  }
  
  .customer-detail-container {
    > .customer-address {
      color: #1c7ed6;
      font-size: small;

      i {
        margin-right: 8px;
      }
    }
  }

  .comment-section {
    > textarea {
      width: 100%;
      resize: none;

      &:focus {
        outline: none;
      }
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