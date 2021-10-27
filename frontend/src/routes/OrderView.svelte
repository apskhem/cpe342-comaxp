<script lang="ts">
  import { navigate } from "svelte-routing";
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
</script>

<template>
  <main>
    {#if mainPayload}
      <div class="layout">
        <section class="customer-detail-container">
          <div class="customer-name">{customerPayload.contactFirstName} {customerPayload.contactLastName}</div>
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
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              {#each mainPayload as item, i}
                <tr>
                  <td>{i + 1}</td>
                  <td>{item.productCode}</td>
                  <td>{null}</td>
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
          <label for="">Comment</label>
          <textarea name="" id="" cols="30" rows="10" bind:value={mainPayload[0].comments} disabled></textarea>
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

  .customer-name {
    font-size: xx-large;
    color: #717177;
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
</style>