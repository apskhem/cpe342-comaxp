<template>
  <div class="full-loading-layout">
    <div class="dot-loading-container">
      <div style="animation-delay: 0ms"></div>
      <div style="animation-delay: 100ms"></div>
      <div style="animation-delay: 200ms"></div>
      <div style="animation-delay: 100ms"></div>
      <div style="animation-delay: 200ms"></div>
      <div style="animation-delay: 300ms"></div>
      <div style="animation-delay: 200ms"></div>
      <div style="animation-delay: 300ms"></div>
      <div style="animation-delay: 400ms"></div>
    </div>
  </div>
</template>

<style lang="scss">
  .full-loading-layout {
    display: flex;
    position: absolute;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;

    > .dot-loading-container {
      $sqaure: 24px;

      display: grid;
      grid-template-columns: repeat(3, $sqaure);
      grid-auto-rows: $sqaure;
      gap: 8px;

      > div {
        background-color: #A8A5A5;
        animation: 800ms dot-animation infinite ease;
      }
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
