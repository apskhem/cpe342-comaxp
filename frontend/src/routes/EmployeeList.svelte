<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import MajorButton from "../components/MajorButton.svelte";
  import { FETCH_ROOT } from "../env.global";
  import { loginToken } from "../stores";

  export let location: string;

  let token = "";
  let list: null | Model.IEmployee[] = null;
  let employeeMap = new Map<number, string>();
  let isAddingMode = false;
  let form: HTMLFormElement;
  const sortStateList = new Array(7).fill(0);

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch(`${FETCH_ROOT}/api/employees`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });
    list = await res.json();

    for (const employee of list ?? []) {
      employeeMap.set(employee.employeeNumber, employee.firstName + " " + employee.lastName);
    }
  };

  start();

  const sortList = (sort: keyof Model.IEmployee, index: number) => {
    return () => {
      if (list) {
        list = list.sort((a, b) => {
          const ac = sortStateList[index]
          const ta = ac === 1 ? b[sort] : a[sort];
          const tb = ac === 1 ? a[sort] : b[sort];

          if (typeof ta === "string" && typeof tb === "string") {
            return ta.localeCompare(tb)
          }
          else if (typeof ta === "number" && typeof tb === "number") {
            return ta - tb;
          }
          else {
            return (ta as any) - (tb as any);
          }
        });
      }

      setSortCaret(index);
    };
  };

  const setSortCaret = (index: number) => {
    const oldSortState = sortStateList[index];

    for (const i of sortStateList.keys()) {
      sortStateList[i] = 0;
    }

    sortStateList[index] = oldSortState === 1 ? 2 : 1;
  };

  const renderCaret = (val: number) => {
    if (val === 1) {
      return "fas fa-sort-up"
    }
    else if (val === 2) {
      return "fas fa-sort-down";
    }
    else {
      return "fas fa-sort";
    }
  };

  const swapCreateMode = () => {
    isAddingMode = !isAddingMode;
  };

  const submitAdding = async () => {
    const formData = new FormData(form);

    try {
      const res = await fetch(`${FETCH_ROOT}/api/employees`, {
        method: "post",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        }),
        body: formData
      });

      const data = await res.json();

      console.log(data);
    }
    catch (err) {

    }
    finally {

    }
  };
</script>

<template>
  <main>
    {#if list && employeeMap.size}
      <div class="layout">
        <div class="create-btn-container">
          <MajorButton
            width="200px"
            label={isAddingMode ? "View Employee" : "Add Employee"}
            on:click={swapCreateMode}
          />
        </div>
        {#if isAddingMode}
          <form class="create-form" bind:this={form} on:submit|preventDefault={submitAdding}>
            <div class="form-half-grid">
              <aside>
                <label for="firstName">First Name</label>
                <input id="firstName" type="text" name="firstName" maxlength="50" required />
              </aside>
              <aside>
                <label for="lastName">Last Name</label>
                <input id="lastName" type="text" name="lastName" maxlength="50" required />
              </aside>
            </div>
            <div class="form-half-grid">
              <aside>
                <label for="extension">Extension</label>
                <input id="extension" type="text" name="extension" required />
              </aside>
              <aside>
                <label for="officeCode">Office Code</label>
                <input id="officeCode" type="text" name="officeCode" required />
              </aside>
            </div>
            <div class="form-half-grid">
              <div>
                <label for="reportsTo">Supervisor</label>
                <input id="reportsTo" type="text" name="reportsTo" required />
              </div>
              <div>
                <label for="jobTitle">Position</label>
                <select name="jobTitle" id="jobTitle" required>
                  <optgroup label="Representative">
                    <option value="Sales Rep" selected>Sales Rep</option>
                  </optgroup>
                  <optgroup label="President">
                    <option value="President">President</option>
                  </optgroup>
                  <optgroup label="Vice President">
                    <option value="VP Marketing">VP Marketing</option>
                    <option value="VP Sales">VP Sales</option>
                  </optgroup>
                  <optgroup label="Sales Manager">
                    <option value="Sales Manager (EMEA)">Sales Manager (EMEA)</option>
                    <option value="Sales Manager (APAC)">Sales Manager (APAC)</option>
                  </optgroup>
                </select>
              </div>
            </div>
            <div>
              <label for="email">Email</label>
              <input id="email" type="email" name="email" maxlength="100" required />
            </div>
            <div>
              <label for="password">Password</label>
              <input id="password" type="password" name="password" minlength="6" required />
            </div>
            <div class="form-btn-container">
              <MajorButton
                width="200px"
                type="submit"
                label="Submit"
              />
            </div>
          </form>
        {:else}
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Full Name <i on:click={sortList("firstName", 1)} class={renderCaret(sortStateList[1])}></i></th>
                  <th>Extension <i on:click={sortList("extension", 2)} class={renderCaret(sortStateList[2])}></i></th>
                  <th>Email <i on:click={sortList("email", 3)} class={renderCaret(sortStateList[3])}></i></th>
                  <th>Reports To <i on:click={sortList("reportsTo", 4)} class={renderCaret(sortStateList[4])}></i></th>
                  <th>Job Title <i on:click={sortList("jobTitle", 5)} class={renderCaret(sortStateList[5])}></i></th>
                  <th>Office Code <i on:click={sortList("officeCode", 6)} class={renderCaret(sortStateList[6])}></i></th>
                </tr>
              </thead>
              <tbody>
                {#each list as el, i}
                  <tr on:click={() => navigate(`/employees/${el.employeeNumber}`)}>
                    <td>{i + 1}</td>
                    <td>{employeeMap.get(el.employeeNumber)}</td>
                    <td>{el.extension}</td>
                    <td>{el.email}</td>
                    <td>{employeeMap.get(el.reportsTo ?? NaN) ?? ""}</td>
                    <td>{el.jobTitle}</td>
                    <td>{el.officeCode}
                      <div on:click|stopPropagation class="row-option">
                        <i class="fas fa-clipboard"></i>
                      </div>
                    </td>
                  </tr>
                {/each}
              </tbody>
            </table>
          </div>
        {/if}
      </div>
    {:else}
      <FullWaiter />
    {/if}
  </main>
</template>

<style lang="scss">
  .layout {
    max-width: 900px;
    margin: 0 auto;
  }

  .create-btn-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 1em;
  }

  .form-btn-container {
    display: flex;
    margin-top: 1em;
    justify-content: center;
  }

  .create-form {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;

    input, select {
      width: 100%;
      padding: 6.4px 12px;
    }

    select {
      height: 38.78px;
      border-radius: 0;
      margin: 0;
      &:focus {
        outline: 0;
      }
    }

    .form-half-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }
  }

  .table-container {
    margin-top: 1em;
  }
</style>
