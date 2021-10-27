<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import SumForm from "../components/SumForm.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { checkoutData, cartProduct, loginToken } from "../stores";

  export let location: string;

  let token = "";
  let cart: Cart;
  let customer: Model.ICustomer;
  let m_checkoutData: { coupon: string, customer: string, preorder: boolean };
  let subtotal = 0;
  let isPending = false;
  let errorMsg = "";
  let comments = "";

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
      navigate("/", { replace: true });
      return;
    }

    const res = await fetch(`${FETCH_ROOT}/api/customers/${m_checkoutData.customer}`, {
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
      const body = new URLSearchParams();
      const compressedOrder = compressOrders(cart);

      // append sending data
      body.append("productCode", compressedOrder.productCode);
      body.append("quantityOrdered", compressedOrder.quantityOrdered);
      body.append("orderLineNumber", compressedOrder.orderLineNumber);
      body.append("priceEach", compressedOrder.priceEach);
      body.append("requiredDate", getFormattedCurrentDate());
      body.append("shippedDate", "");
      body.append("status", m_checkoutData.preorder ? "preorder" : "in process");
      body.append("customerNumber", `${customer.customerNumber}`);
      body.append("comments", `${comments}`);
      body.append("discountCode", `${m_checkoutData.coupon}`);
      body.append("upfrontPrice", m_checkoutData.preorder ? Math.round(subtotal / 2).toFixed(0) : "");

      console.log(body.toString());

      // send request
      const res = await fetch(`${FETCH_ROOT}/api/orders`, {
        method: "post",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        }),
        body
      });

      const data = await res.json();

      // handle request
      console.log(data);

      if (res.status === 200) {
        // reset
        checkoutData.set({ coupon: "", customer: "", preorder: false });
        cartProduct.set(new Map());

        // redirect to home
        navigate("/", { replace: true });
      }
      else {
        throw new Error("the status is not 200");
      }
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
        <section class="customer-detail-container">
          <div class="customer-name">{customer.customerName}</div>
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
              {#each Array.from(cart) as [ code, [ count, item ]], i}
                <tr>
                  <td>{i + 1}</td>
                  <td>{code}</td>
                  <td>{item.productName}</td>
                  <td>{count.toLocaleString("en")}</td>
                  <td>${(count * Number.parseFloat(item.buyPrice)).toFixed(2)}
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
          <textarea name="" id="" cols="30" rows="10" bind:value={comments}></textarea>
        </section>
        <SumForm
          enableInputs={false}
          sum={subtotal}
          label={m_checkoutData.preorder ? "Place Pre-Order" : "Place Order"}
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