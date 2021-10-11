<script lang="ts">
  import FullWaiter from "../components/FullWaiter.svelte";
import VendorView from "../components/VendorView.svelte";

  export let location: string;

  let isRequesing = false;
  const catalogMap = new Map<string, Model.IProduct[]>();

  const start = async () => {
    isRequesing = true;
    const res = await fetch("https://comaxp.herokuapp.com/api/catalogs");
    const data: Response.GetProductList = await res.json();

    if (data) {
      for (const d of data) {
        addCatalog(d);
      }
    }

    console.log(catalogMap);
    isRequesing = false;
  };

  const addCatalog = (item: Model.IProduct) => {
    // if the vendor is exist
    if (catalogMap.has(item.productVendor)) {
      const arr = catalogMap.get(item.productVendor)

      if (!arr) {
        throw new Error("Assertion failed");
      }

      arr.push(item);
    }
    // otherwise
    else {
      catalogMap.set(item.productVendor, [ item ]);
    }
  };

  start();
</script>

<template>
  <main>
    {#if isRequesing}
      <FullWaiter />
    {:else}
      <div class="layout">
        {#each Array.from(catalogMap) as [ vendor, products ]}
          <VendorView vendor={vendor} products={products} />
        {/each}
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