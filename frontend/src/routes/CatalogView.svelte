<script lang="ts">
  import FullWaiter from "../components/FullWaiter.svelte";
import VendorView from "../components/VendorView.svelte";
  import { loginToken } from "../stores";

  export let id: string;
  export let location: string;

  let token: string;
  let products: Model.IProduct[];
  let isRequesting = false;

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    isRequesting = true;
    const res = await fetch(`https://comaxp.herokuapp.com/api/catalogs/${id}`);
    products = await res.json();

    isRequesting = false;
  };

  start();
</script>

<template>
  <main>
    {#if isRequesting}
      <FullWaiter />
    {:else}
      <div class="layout">
        <VendorView vendor={id} products={products} />
      </div>
    {/if}
  </main>
</template>


<style lang="scss">
  .layout {
    max-width: 900px;
    margin: 0 auto;
  }
</style>