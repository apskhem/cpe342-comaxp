<script lang="ts">
  import { navigate } from "svelte-routing";
  import FullWaiter from "../components/FullWaiter.svelte";
  import { loginToken } from "../stores";

  export let location: string;

  let token = "";
  let list: null | Model.IEmployee[] = null;
  let employeeMap = new Map<number, string>();
  const sortStateList = new Array(6).fill(0);

  loginToken.subscribe((value) => token = value);

  const start = async () => {
    const res = await fetch("https://comaxp.herokuapp.com/api/employees", {
      method: "get",
      headers: new Headers({
        "Authorization": `Bearer ${token}`
      })
    });
    const data: Response.GetEmployeeList = await res.json();

    list = data;

    console.log(data);

    for (const employee of data) {
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
            return ta - tb;
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
</script>

<template>
  <main>
    {#if list && employeeMap.size}
      <div class="layout table-container">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Full Name <i on:click={sortList("firstName", 1)} class={renderCaret(sortStateList[1])}></i></th>
              <th>Extension <i on:click={sortList("extension", 2)} class={renderCaret(sortStateList[2])}></i></th>
              <th>Email <i on:click={sortList("email", 3)} class={renderCaret(sortStateList[3])}></i></th>
              <th>Reports To <i on:click={sortList("reportsTo", 4)} class={renderCaret(sortStateList[4])}></i></th>
              <th>Job Title <i on:click={sortList("jobTitle", 5)} class={renderCaret(sortStateList[5])}></i></th>
            </tr>
          </thead>
          <tbody>
            {#each list as el, i}
              <tr on:click={() => navigate(`/employee/${el.employeeNumber}`)}>
                <td>{i + 1}</td>
                <td>{employeeMap.get(el.employeeNumber)}</td>
                <td>{el.extension}</td>
                <td>{el.email}</td>
                <td>{employeeMap.get(el.reportsTo ?? NaN) ?? ""}</td>
                <td>{el.jobTitle}
                  <div on:click|stopPropagation class="row-option">
                    <i class="fas fa-clipboard"></i>
                  </div>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
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

  .table-container {
    margin-top: 2em;
    border: 1px solid #E1DEDE;
    overflow-x: auto;
  }

  table {
    position: relative;
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    z-index: 0;

    > thead {

      > tr {
        > th {
          position: relative;
          padding: 8px;
          border: 1px solid #E1DEDE;
          text-align: left;

          > i {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translate(0, -50%);
            cursor: pointer;
          }
        }

        /* no. */
        &:first-child {
          > th:first-child {
            text-align: center;
            width: 48px;
          }
        }
      }
    }

    > tbody {
      > tr {
        transition: background-color 0.3s;

        &:hover {
          background-color: whitesmoke;

          > td:last-child {
            > div {
              opacity: 1;
              transform: translate(-100%, -50%);
            }
          }
        }

        &.deleting {
          background-color: #f6dfeb;
        }

        > td {
          padding: 4px 8px;
          white-space: nowrap;
          border: 1px solid #E1DEDE;
          overflow: hidden;
          text-overflow: ellipsis;

          &:first-child {
            text-align: center;
            font-weight: bold;
          }

          &:last-child {
            position: relative;

            > div {
              display: flex;
              align-items: center;
              gap: 0.5em;
              top: 50%;
              right: 0;
              position: absolute;
              height: 100%;
              transform: translate(0, -50%);
              transition: 300ms;
              opacity: 0;

              > i {
                cursor: pointer;
              }
            }
          }

          &:empty:after {
            content: "N/A";
            color: white;
            font-weight: bold;
            background-color: #AB1A1A;
            padding: 0 4px;
            border-radius: 2px;
          }

          &.editor {
            text-align: center;

            > i {
              cursor: pointer;

              &:not(:first-child) {
                margin-left: 8px;
              }

              &:first-child {
                color: #5d534a;
              }

              &:last-child {
                color: #cd5d7d;
              }
            }
          }

          &.pending {
            width: 68px;
            height: 28px;
          }

          ol {
            margin: 0;
            padding-left: 2em;

            li {
              list-style-type: circle !important;
            }
          }
        }

        &.disabled {
          background-color: lightgrey;

          i {
            color: #5d534a !important;
          }
        }
      }
    }
  }
</style>
