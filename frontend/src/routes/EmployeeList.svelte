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
  let supervisorMap = new Map<number, string>();
  let isInAddingMode = false;
  let isAddingPending = false;
  let form: HTMLFormElement;
  let errMsg = "";

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch(`${FETCH_ROOT}/api/employees`, {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });

    list = await res.json();

    setSupervisorMap(list ?? [])
    setEmployeeMap(list ?? []);
  };

  start();

  const setEmployeeMap = (list: Model.IEmployee[]) => {
    for (const employee of list) {
      employeeMap.set(employee.employeeNumber, employee.firstName + " " + employee.lastName);
    }
  };

  const setSupervisorMap = (list: Model.IEmployee[]) => {
    for (const employee of list) {
      if (employee.jobTitle !== "Sales Rep") {
        supervisorMap.set(employee.employeeNumber, employee.jobTitle);
      }
    }
  };

  const swapCreateMode = () => {
    isInAddingMode = !isInAddingMode;
    errMsg = "";
  };

  const submitAdding = async () => {
    if (isAddingPending) {
      return;
    }

    isAddingPending = true;
    errMsg = "";

    try {
      // send request
      const res = await fetch(`${FETCH_ROOT}/api/employees`, {
        method: "post",
        headers: new Headers({
          "Authorization": `Bearer ${token}`
        }),
        body: new FormData(form)
      });

      const data = await res.json();

      // handle request
      if (data.errors) {
        throw new Error(data.errors);
      }
      else {
        form.reset();
      }
    }
    catch (err) {
      errMsg = `${err}`;
      console.log(err);
    }
    finally {
      isAddingPending = false;
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
            label={isInAddingMode ? "View Employees" : "Add An Employee"}
            on:click={swapCreateMode}
          />
        </div>
        {#if isInAddingMode}
          <form class="create-form" bind:this={form} on:submit|preventDefault={submitAdding}>
            <div class="form-half-grid">
              <aside>
                <label for="firstName">First Name</label>
                <input id="firstName" type="text" name="firstName" maxlength="50" disabled={isAddingPending} required />
              </aside>
              <aside>
                <label for="lastName">Last Name</label>
                <input id="lastName" type="text" name="lastName" maxlength="50" disabled={isAddingPending} required />
              </aside>
            </div>
            <div class="form-half-grid">
              <aside>
                <label for="extension">Extension</label>
                <input id="extension" type="text" name="extension" disabled={isAddingPending} required />
              </aside>
              <aside>
                <label for="officeCode">Office Code</label>
                <input id="officeCode" type="text" name="officeCode" disabled={isAddingPending} required />
              </aside>
            </div>
            <div class="form-half-grid">
              <div>
                <label for="reportsTo">Supervisor</label>
                <select name="reportsTo" id="reportsTo" disabled={isAddingPending} required>
                  {#each Array.from(supervisorMap) as [ emNum, position ]}
                    <option value={emNum}>{employeeMap.get(emNum)} ({position})</option>
                  {/each}
                </select>
              </div>
              <div>
                <label for="jobTitle">Position</label>
                <select name="jobTitle" id="jobTitle" disabled={isAddingPending} required>
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
              <input id="email" type="email" name="email" maxlength="100" disabled={isAddingPending} required />
            </div>
            <div>
              <label for="password">Password</label>
              <input id="password" type="password" name="password" minlength="6" disabled={isAddingPending} required />
            </div>
            <div class="form-btn-container">
              <MajorButton
                width="200px"
                type="submit"
                label="Submit"
                isPending={isAddingPending}
              />
            </div>
            {#if errMsg}
              <div class="err-msg">{errMsg}</div>
            {/if}
          </form>
        {:else}
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Full Name</th>
                  <th>Extension</th>
                  <th>Email</th>
                  <th>Reports</th>
                  <th>Job Title</th>
                  <th>Office Code</th>
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

      &[required]:empty {
        border-color: darkorange;
      }
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

  .err-msg {
    color: #AB1A1A;
    font-size: small;
    text-align: center;
  }
</style>
