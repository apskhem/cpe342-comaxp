<script lang="ts">
  import FullWaiter from "../components/FullWaiter.svelte";
  import VendorView from "../components/VendorView.svelte";
  import { FETCH_ROOT } from "../env.global";

  export let location: string;

  let isRequesting = false;
  const catalogMap = new Map<string, Model.IProduct[]>();

  const start = async () => {
    isRequesting = true;
    const res = await fetch(`${FETCH_ROOT}/api/catalogs`);
    const data: Response.GetProductList = await res.json();

    if (data) {
      for (const d of data) {
        addCatalog(d);
      }
    }

    console.log(catalogMap);
    isRequesting = false;
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
    {#if isRequesting}
      <FullWaiter />
    {:else}
      <div class="layout">
        {#each Array.from(catalogMap) as [ vendor, products ]}
          <VendorView
            vendor={vendor}
            products={products}
            limit={4}
            expand={true}
          />
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