DROP TABLE IF EXISTS public.employees;

CREATE TABLE IF NOT EXISTS public.employees
(
    "employeeNumber" serial PRIMARY KEY,
    "lastName" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "firstName" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    extension character varying(10) COLLATE pg_catalog."default" NOT NULL,
    email character varying(100) COLLATE pg_catalog."default" NOT NULL,
    "officeCode" character varying(10) COLLATE pg_catalog."default" NOT NULL,
    "reportsTo" integer,
    "jobTitle" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    password character varying(100) COLLATE pg_catalog."default" NOT NULL
)

TABLESPACE pg_default;

ALTER TABLE public.employees
	ADD CONSTRAINT employees_ibfk_1
	FOREIGN KEY ("reportsTo")
	REFERENCES public.employees("employeeNumber")
	ON UPDATE CASCADE
	ON DELETE SET NULL,
	ADD CONSTRAINT employees_ibfk_2
	FOREIGN KEY ("officeCode")
	REFERENCES public.offices("officeCode")
	ON UPDATE CASCADE
	ON DELETE SET NULL;

ALTER TABLE public.employees
    OWNER to dosrucomwumkym;