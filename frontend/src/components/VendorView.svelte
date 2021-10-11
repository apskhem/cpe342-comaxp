<script lang="ts">
  import { navigate } from "svelte-routing";
  import ProductCard from "../components/ProductCard.svelte";

  export let expand = false;
  export let limit = Infinity;
  export let vendor: string;
  export let products: Model.IProduct[];
</script>

<template>
  <div class="vendor-view">
    <div class="vendor-view-header">
      <div># {vendor} ({products.length})</div>
      {#if expand}
        <div on:click={() => navigate(`catalog/${vendor}`)} class="see-more">see more</div>
      {/if}
    </div>
    <div class="item-layout">
      {#each products.slice(0, limit) as p}
        <ProductCard data={p} />
      {/each}
    </div>
  </div>
</template>

<style lang="scss">
  .vendor-view {
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

      .see-more {
        cursor: pointer;
      }
    }

    .item-layout {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1em;
      grid-auto-rows: 312px;
      margin-top: 1em;
    }
  }
</style>
