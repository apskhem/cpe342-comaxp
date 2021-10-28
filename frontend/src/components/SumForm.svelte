<script lang="ts">
  export let enableInputs = false;
  export let isPending = false;
  export let label: string;
  export let sum: number;
  export let errorMsg: string;
  export let discountCode: Model.IDiscountCode | null = null;
  export let onSubmit: (coupon: string, customer: string) => void;
  
  let customer = "";
  let coupon = "";
</script>

<template>
  <form class="total-layout" on:submit|preventDefault={() => onSubmit(coupon, customer)}>
    <div class="total-container">
      <div class="total-grid">
        <aside>Subtotal:</aside>
        <aside>${sum.toLocaleString("en")}</aside>
      </div>
      {#if discountCode}
        <div class="total-grid">
          <aside>Discounted:</aside>
          <aside>{-discountCode.discountPrice}</aside>
        </div>
      {/if}
      <div class="total-grid">
        <aside>Shipping Fee:</aside>
        <aside>Free</aside>
      </div>
      <div class="total-grid total">
        <aside>Total:</aside>
        <aside>${(sum - (discountCode?.discountPrice || 0)).toLocaleString("en")}</aside>
      </div>
      {#if enableInputs}
        <input
          type="text"
          placeholder="Discount code"
          bind:value={coupon}
          disabled={isPending}
          minlength="8"
          maxlength="8"
        >
        <input
          type="text"
          placeholder="Customer name or code"
          bind:value={customer}
          disabled={isPending}
          required
        >
      {/if}
      <button type="submit" class="checkout-btn">
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
      {#if errorMsg}
        <div class="err-msg">{errorMsg}</div>
      {/if}
    </div>
  </form>
</template>

<style lang="scss">
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

  .total-layout {
    display: flex;
    justify-content: end;
    
    > .total-container {
      margin-top: 2em;
      width: 240px;

      > .total-grid {
        display: grid;
        grid-template-columns: 96px 1fr;

        > :last-child {
          text-align: end;
        }
      }

      > .total {
        border-top: 1px solid #A39D9D;
        margin-top: 8px;
        padding-top: 8px;
        font-weight: bold;
      }
    }

    input {
      width: 100%;

      &:first-of-type {
        margin-top: 1em;
        border-bottom: 0;
      }
    }
  }

  .checkout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 1em;
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

  .err-msg {
    color: #AB1A1A;
    font-size: small;
    text-align: center;
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