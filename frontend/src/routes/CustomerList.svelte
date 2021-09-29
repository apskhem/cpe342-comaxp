<script lang="ts">
  import FullWaiter from "../components/FullWaiter.svelte";

  export let location: string;
</script>

<template>
  <main>
    <FullWaiter />
    <div class="layout">

    </div>
  </main>
</template>

<style lang="scss">
  .layout {
    max-width: 900px;
    margin: 0 auto;
  }
</style>
