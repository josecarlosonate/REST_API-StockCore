# StockCore

API REST orientada a la gestión de inventario y operaciones comerciales desarrollada con **Laravel 13**.

StockCore está enfocado en implementar prácticas y problemas habituales del desarrollo backend, como validación de datos, relaciones entre entidades, consistencia transaccional, control de concurrencia, seguridad, manejo estructurado de errores, paginación, filtrado y pruebas automatizadas.

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
- Registra el usuario responsable de cada movimiento de inventario.
- Permite consultar el historial general o los movimientos de un producto específico.
- Permite filtrar los movimientos por producto y tipo de operación.
- Valida las reglas de negocio antes de modificar las existencias.
- Protege las actualizaciones de inventario mediante transacciones y control de concurrencia.
- Permite registrar y administrar clientes.

El proyecto evolucionará para incorporar pedidos y ventas, relacionando clientes y productos con las operaciones de inventario.

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

StockCore se desarrolla de forma incremental. Nuevas tecnologías, herramientas y componentes de infraestructura se incorporan únicamente cuando son necesarios para un caso de uso implementado.

Actualmente incluye gestión de productos, categorías, proveedores, clientes e inventario, autenticación con Sanctum, trazabilidad de movimientos de stock, control de concurrencia y pruebas automatizadas.

---

## Autor

**Jose Carlos Oñate Rodríguez**

Proyecto de portafolio --- Laravel 13 / PostgreSQL / REST API
