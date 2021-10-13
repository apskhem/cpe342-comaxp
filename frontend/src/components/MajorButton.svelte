<script lang="ts">
  export let type: HTMLButtonElement["type"] = "button";
  export let label: string;
  export let width = "100%";
</script>

<template>
  <button on:click type={type} style={`width: ${width};`}>{label}</button>
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
</style>