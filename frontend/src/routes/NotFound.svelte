<script lang="ts">
  export let location: string;
</script>

<template>
  <main>
    <div class="layout">
      <div class="not-found-text">Error: Page Not Found</div>
    </div>
  </main>
</template>

<style lang="scss">
  main {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .layout {
    max-width: 900px;
    margin: 0 auto;
  }

  .not-found-text {
    font-weight: bold;
    font-size: 48pt;
  }
</style>