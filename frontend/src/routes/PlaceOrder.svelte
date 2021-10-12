<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
import SumForm from "../components/SumForm.svelte";
  import { checkoutData, cartProduct, loginToken } from "../stores";

  export let location: string;

  let token = "";
  let cart: Cart;
  let customer: Model.ICustomer;
  let m_checkoutData: { coupon: string, customer: string };
  let subtotal = 0;
  let isPending = false;
  let errorMsg = "";

  loginToken.subscribe((value) => token = value);
  checkoutData.subscribe((value) => m_checkoutData = value);
  cartProduct.subscribe((value: Cart) => {
    cart = value;

    let sum = 0;
    for (const [, [ count, item ]] of value) {
      sum += count * Number.parseFloat(item.buyPrice);
    }
    subtotal = sum;
  });

  const start = async () => {
    if (!m_checkoutData.customer) {
      return;
    }

    const res = await fetch(`https://comaxp.herokuapp.com/api/customers/${m_checkoutData.customer}`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });

    customer = (await res.json())[0];
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

  const compressOrders = (cart: Cart) => {
    const res = {
      "productCode": [] as any[],
      "quantityOrdered": []  as any[],
      "orderLineNumber": []  as any[],
      "priceEach": []  as any[]
    };

    for (const [ , [ count , item ]] of cart) {
      res.productCode.push(item.productCode);
      res.quantityOrdered.push(count);
      res.orderLineNumber.push("1");
      res.priceEach.push(item.buyPrice);
    }

    return {
      "productCode": res.productCode.join(","),
      "quantityOrdered": res.quantityOrdered.join(","),
      "orderLineNumber": res.orderLineNumber.join(","),
      "priceEach": res.priceEach.join(",")
    };
  };
  
  const getFormattedCurrentDate = () => {
    return new Date().toLocaleDateString("fr").split("/").reverse().join("-");
  }

  const placeOrder = async () => {
    isPending = true;
    errorMsg = "";

    try {
      const body = new FormData();
      const compressedOrder = compressOrders(cart);

      body.set("productCode", compressedOrder.productCode);
      body.set("quantityOrdered", compressedOrder.quantityOrdered);
      body.set("orderLineNumber", compressedOrder.orderLineNumber);
      body.set("priceEach", compressedOrder.priceEach);
      body.set("requiredDate", getFormattedCurrentDate());
      body.set("status", "in process");
      body.set("customerNumber", `${customer.customerNumber}`);

      if (m_checkoutData.coupon) {
        body.set("discountCode", `${m_checkoutData.coupon}`);
      }

      const res = await fetch(`https://comaxp.herokuapp.com/api/orders`, {
        method: "post",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        }),
        body
      });

      const data = await res.json();

      console.log(data);
    }
    catch (err) {
      errorMsg = `${err}`;
    }
    finally {
      isPending = false;
    }
  };
</script>

<template>
  <main>
    {#if customer}
      <div class="layout">
        <div class="customer-detail-container">
          <div class="customer-name">{customer.contactFirstName} {customer.contactLastName}</div>
          <div class="customer-address">
            <div>
              <i class="fas fa-map-marker-alt"></i>
              <span>{getCustomerAddress(customer)}</span>
            </div>
            <div>
              <i class="fas fa-phone-alt"></i>
              <span>{customer.phone}</span>
            </div>
          </div>
        </div>
        <div class="table-container">
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
              {#each Array.from(cart) as [ code, [ count, item ]], i}
                <tr>
                  <td>{i + 1}</td>
                  <td>{code}</td>
                  <td>{item.productName}</td>
                  <td>{count.toLocaleString("en")}</td>
                  <td>${count * Number.parseFloat(item.buyPrice)}
                    <div on:click|stopPropagation class="row-option">
                      <i class="fas fa-clipboard"></i>
                    </div>
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        </div>
        <SumForm
          enableInputs={false}
          sum={subtotal}
          label="Place Order"
          isPending={isPending}
          errorMsg={errorMsg}
          onSubmit={placeOrder}
        />
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
</style>