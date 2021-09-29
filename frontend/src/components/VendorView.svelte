<script lang="ts">
  import ProductCard from "../components/ProductCard.svelte";
</script>

<template>
  <div class="vendor-view">
    <div class="vendor-view-header">
      <div># Autoart Studio Design</div>
      <div class="see-more">see more</div>
    </div>
    <div class="item-layout">
      <ProductCard />
      <ProductCard />
      <ProductCard />
      <ProductCard />
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
