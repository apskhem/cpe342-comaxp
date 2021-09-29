declare module "*.svelte" {
  export { SvelteComponentDev as default } from "svelte/internal";
}

module Response {
  interface Login {
    Token: string;
  }

  type GetEmployeeList = IEmployee[];
}

module Model {
  interface IEmployee {
    email: string;
    employeeNumber: number;
    extension: string;
    firstName: string;
    lastName: string;
    jobTitle: string;
    officeCode: string;
    reportsTo: null | number;
  }
}