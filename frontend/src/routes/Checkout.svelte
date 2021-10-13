<script lang="ts">
  import { navigate } from "svelte-routing";
  import ProductCard from "../components/ProductCard.svelte";
  import SumForm from "../components/SumForm.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { checkoutData, cartProduct, loginToken } from "../stores";

  export let location: string;

  let cartRef: Cart;
  let filtered: Map<string, Cart>;
  let subtotal = 0;
  let isPending = false;
  let token = "";
  let errorMsg = "";

  $: {
    let sum = 0;
    for (const [, items] of filtered) {
      for (const [ , [ count, item ]] of items) {
        sum += count * Number.parseFloat(item.buyPrice);
      }
    }

    subtotal = sum;
  }

  loginToken.subscribe((value) => token = value);

  cartProduct.subscribe((cart: Cart) => {
    cartRef = cart;

    const temp = new Map<string, Cart>();

    for (const entry of cart) {
      const [ code, [ count, item ]] = entry;
      
      // if the vendor is exist
      if (temp.has(item.productVendor)) {
        temp.get(item.productVendor)?.set(code, [ count, item ]);
      }
      // otherwise
      else {
        const map = new Map([[ code, [ count, item ]]]) as Cart;
        temp.set(item.productVendor, map);
      }
    }

    filtered = temp;
  });

  const increaseCount = (itemCode: string) => {
    return () => {
      const entry = cartRef.get(itemCode);

      if (!entry) {
        return;
      }

      const item = entry[1];
      entry[0] = Math.min(item.quantityInStock, entry[0] + 1);

      cartProduct.set(cartRef);
    };
  };

  const decreaseCount = (itemCode: string) => {
    return () => {
      const entry = cartRef.get(itemCode);

      if (!entry) {
        return;
      }

      entry[0] = Math.max(1, entry[0] - 1);

      cartProduct.set(cartRef);
    };
  };

  const setCount = (itemCode: string) => {
    return (value: number) => {
      const entry = cartRef.get(itemCode);

      if (!entry) {
        return;
      }

      const item = entry[1];
      let ranged = Math.min(item.quantityInStock, value);
      ranged = Math.max(1, ranged);

      entry[0] = ranged;

      cartProduct.set(cartRef);
    };
  };

  const deleteItem = (itemCode: string) => {
    return () => {
      cartRef.delete(itemCode);
      cartProduct.set(cartRef);
    };
  };

  const checkout = async (coupon: string, customer: string) => {
    isPending = true;
    errorMsg = "";
    
    try {
      const res = await fetch(`${FETCH_ROOT}/api/customers/${customer}`, {
        method: "get",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        })
      });
      const data = (await res.json());

      // error guard
      if (!data || !data.length) {
        throw new Error("Incorrect customer code");
      }

      checkoutData.set({ coupon, customer });
      
      navigate("/placeorder");
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
    <div class="layout">
      {#if filtered.size}
        {#each Array.from(filtered) as [ vendor, items] }
          <div class="vendor-view-header">
            <div># {vendor}</div>
          </div>
          {#each Array.from(items) as [ code, [ count, item ]] }
            <div class="cart-grid">
              <aside class="cart-container">
                <ProductCard data={item} />
              </aside>
              <aside class="cart-detail">
                <div>
                  <div class="detail-header">Quantity:</div>
                  <div class="quantity-input-container">
                    <button on:click={decreaseCount(code)}>
                      <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" value={count} on:blur={(e) => setCount(code)(e.currentTarget.valueAsNumber)} />
                    <button on:click={increaseCount(code)}>
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                  <div class="detail-header">Total Price:</div>
                  <div>${(Number.parseFloat(item.buyPrice) * count).toLocaleString("en")}</div>
                  <div class="delete-btn" on:click={deleteItem(code)}>Delete</div>
                </div>
              </aside>
            </div>
          {/each}
        {/each}
        <SumForm
          enableInputs={true}
          sum={subtotal}
          label="Checkout"
          isPending={isPending}
          errorMsg={errorMsg}
          onSubmit={checkout}
        />
      {:else}
        <div class="empty-basket">No product found in the basket.</div>
      {/if}
    </div>
  </main>
</template>


<style lang="scss">
  .layout {
    max-width: 900px;
    margin: 0 auto;
    padding-top: 2em;
  }

  .cart-grid {
    display: grid;
    grid-template-columns: 211px 1fr;
    gap: 1em;
    margin-top: 1em;

    > .cart-container {

    }

    > .cart-detail {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 1em;

      .detail-header {
        margin-top: 1em;
        margin-bottom: 4px;
        font-weight: bold;
      }

      .quantity-input-container {
        display: grid;
        grid-template-columns: 38px 1fr 38px;
        width: 128px;
        height: 38px;

        > input {
          width: 100%;
          height: 100%;
          text-align: center;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }

        > button {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 100%;
          height: 100%;
          color: #717177;
        }
      }

      .delete-btn {
        margin-top: 1em;
        padding: 8px;
        color: #AB1A1A;
        border: 1px solid #AB1A1A;
        text-align: center;
        cursor: pointer;
        transition: 300ms;

        &:hover {
          color: white;
          background-color: #AB1A1A;
        }
      }
    }
  }

  .empty-basket {
    text-align: center;
    font-weight: bold;
    font-size: 36pt;
  }

  .vendor-view-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 2em;
    padding-bottom: 0.5em;
    border-bottom: 1px solid #A39D9D;

    > div {
      color: #A39D9D;
    }
  }
</style>