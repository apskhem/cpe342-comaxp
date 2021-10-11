<script lang="ts">
  import ProductCard from "../components/ProductCard.svelte";
  import { cartProduct } from "../stores";

  let cart: Map<string, [ number, Model.IProduct ]>;

  cartProduct.subscribe((value) => cart = value);
</script>

<template>
  <main>
    <div class="layout">
      {#if cart.size}
        {#each Array.from(cart) as [, [, item]] }
          <div class="vendor-view-header">
            <div># {"safasdfasdf"}</div>
          </div>
          <div class="cart-grid">
            <aside>
              <ProductCard data={item} />
            </aside>
            <aside>

            </aside>
          </div>
        {/each}
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