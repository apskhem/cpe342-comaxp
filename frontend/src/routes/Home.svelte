<script lang="ts">
  export let location: string;
</script>

<template>
  <main>
    <div class="layout">
      <div>main page</div>
    </div>
  </main>
</template>

<style lang="scss">
  main {
    min-height: calc(100vh - 54px - 28px);
  }

  .layout {
    max-width: 900px;
    margin: 0 auto;
  }
</style>