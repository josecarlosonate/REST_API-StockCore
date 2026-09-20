# StockCore

StockCore es una API REST orientada a la gestión de inventario y operaciones comerciales desarrollada con **Laravel**..

El proyecto busca representar la lógica de un sistema de inventario real, gestionando productos, categorías,
proveedores, existencias y movimientos de stock, con una arquitectura preparada para incorporar posteriormente clientes, pedidos y ventas.

A diferencia de una API CRUD básica, StockCore está enfocado en implementar prácticas y problemas habituales del desarrollo backend, como validación de datos, relaciones entre entidades, consistencia transaccional, control de concurrencia, seguridad, manejo estructurado de errores, paginación, filtrado y pruebas automatizadas.

---

## Contenido

- [Sobre el proyecto](#sobre-el-proyecto)
- [Stack tecnológico](#stack-tecnológico)
- [API](#api)
- [Estado del proyecto](#estado-del-proyecto)

---

## Sobre el proyecto

Modela las operaciones principales de un sistema de gestión de inventario:

- Permite gestionar productos y categorías.
- Permite registrar y administrar proveedores.
- Permite asociar productos con categorías y proveedores.
- Mantiene el inventario individual de cada producto.
- Permite configurar niveles mínimos y máximos de stock.
- Registra entradas, salidas y ajustes de inventario.
- Mantiene un historial de movimientos con el stock anterior y resultante.
- Permite consultar el historial general o los movimientos de un producto específico.
- Permite filtrar los movimientos por producto y tipo de operación.
- Valida las reglas de negocio antes de modificar las existencias.
- Protege las actualizaciones de inventario mediante transacciones y control de concurrencia.

El proyecto evolucionará para incorporar clientes, pedidos y ventas, integrando estas operaciones con el control de inventario.

El objetivo del proyecto es demostrar el desarrollo de una **API REST profesional con Laravel**, aplicando diseño de APIs, relaciones con Eloquent, validación, autenticación, consistencia transaccional, control de concurrencia y pruebas automatizadas.

---

## Stack tecnológico

### Backend

- PHP 8.5
- Laravel 13
- Laravel Sanctum

### Base de datos

- PostgreSQL

### Testing y calidad

- PHPUnit
- Laravel Pint

### Desarrollo y herramientas

- Composer
- Git
- GitHub
- Postman

---

## API

La API está versionada desde sus primeros endpoints públicos:

`/api/v1`

---

## Estado del proyecto

🚧 **En desarrollo activo**

StockCore se está desarrollando de forma incremental. Nuevas tecnologías, herramientas y componentes de infraestructura se incorporarán únicamente cuando sean necesarios para un caso de uso implementado.
