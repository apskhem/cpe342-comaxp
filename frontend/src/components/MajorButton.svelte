<script lang="ts">
  export let isPending = false;
  export let type: HTMLButtonElement["type"] = "button";
  export let label: string;
  export let width = "100%";
</script>

<template>
  <button on:click type={type} style={`width: ${width};`} disabled={isPending}>
  {#if isPending}
    <div class="square-loading-container">
      <div style="animation-delay: 0ms"></div>
      <div style="animation-delay: 100ms"></div>
      <div style="animation-delay: 200ms"></div>
    </div>
  {:else}
    {label}
  {/if}
  </button>
</template>

<style lang="scss">
  button {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 8px;
    color: #AB1A1A;
    border: 1px solid #AB1A1A;
    background-color: transparent;
    cursor: pointer;
    border-radius: 0;
    transition: 300ms;

    &:disabled {
      pointer-events: none;
    }

    &:hover {
      color: white;
      background-color: #AB1A1A;

      .square-loading-container {
        > div {
          background-color: white;
        }
      }
    }
  }

  .square-loading-container {
    display: flex;
    gap: 8px;
    
    > div {
      width: 24px;
      height: 24px;
      background-color: #AB1A1A;
      animation: 800ms dot-animation infinite ease;
    }
  }

  @keyframes dot-animation {
    0% {
      transform: scale(1);
    }
    50% {
      transform: scale(0);
    }
    100% {
      transform: scale(1);
    }
  }
</style>