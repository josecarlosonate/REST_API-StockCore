<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StockCore API</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-stockcore-bg text-stockcore-text">
    <main class="mx-auto w-[90%] max-w-[1100px]">

        <header class="flex h-20 items-center justify-between border-b border-[#21262d]">
            <a href="/" class="flex items-center gap-3 text-lg font-semibold text-[#e6edf3] no-underline">
                <span
                    class="grid h-9 w-9 place-items-center rounded-lg border border-[#30363d] bg-[#161b22] font-bold text-[#3fb950]">
                    S
                </span>

                <span>StockCore API</span>
            </a>

            <div class="flex items-center gap-2 rounded-full border border-[#238636] px-3 py-2 text-sm text-[#3fb950]">
                <span class="h-2 w-2 rounded-full bg-[#3fb950]"></span>
                API Online
            </div>
        </header>

        <section class="py-20">
            <div class="max-w-3xl">
                <p class="mb-4 font-mono text-sm font-medium text-stockcore-green">
                    REST API · Laravel 13
                </p>

                <h1 class="text-5xl font-bold tracking-tight">
                    A REST API for,
                    <span class="text-stockcore-green">inventory and stock management.</span>
                </h1>

                <p class="mt-6 max-w-2xl text-lg leading-8 text-stockcore-muted">
                    StockCore is a REST API for managing products, suppliers,
                    customers, inventory and stock movements with authentication,
                    transactional consistency and concurrency control.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="https://stockcore-api.docs.buildwithfern.com"
                        class="rounded-md bg-stockcore-green px-5 py-3 font-medium text-stockcore-dark transition hover:opacity-90">
                        API Documentation
                    </a>

                    <a href="https://github.com/josecarlosonate/StockCore" target="_blank" rel="noopener noreferrer"
                        class="rounded-md border border-stockcore-border bg-stockcore-surface px-5 py-3 font-medium transition hover:border-stockcore-muted">
                        GitHub Repository
                    </a>
                </div>
            </div>
        </section>

        <section class="border-t border-stockcore-border py-10">
            <div class="mb-4">
                <p class="font-mono text-xs uppercase tracking-wider text-stockcore-muted">
                    API Base URL
                </p>

                <h2 class="mt-2 text-xl font-semibold">
                    Local development
                </h2>
            </div>

            <div
                class="flex items-center justify-between rounded-lg border border-stockcore-border bg-stockcore-surface px-5 py-4">

                <code id="api-base-url" class="font-mono text-sm text-stockcore-green">
                    http://localhost:8000/api/v1
                </code>

                <div class="flex items-center gap-2">
                    <span
                        class="rounded-md border border-stockcore-border px-3 py-1.5 font-mono text-xs text-stockcore-muted">
                        v1
                    </span>

                    <button id="copy-api-url" type="button"
                        class="cursor-pointer rounded-md border border-stockcore-border px-3 py-1.5 font-mono text-xs text-stockcore-muted transition hover:border-stockcore-green hover:text-stockcore-green">
                        Copy
                    </button>
                </div>
            </div>
        </section>

        <section class="border-t border-stockcore-border py-10">
            <div class="mb-6">
                <p class="font-mono text-xs uppercase tracking-wider text-stockcore-muted">
                    Modules
                </p>

                <h2 class="mt-2 text-xl font-semibold">
                    API capabilities
                </h2>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">

                <div class="rounded-lg border border-stockcore-border bg-stockcore-surface p-5">
                    <h3 class="font-semibold">Products</h3>
                    <p class="mt-2 text-sm leading-6 text-stockcore-muted">
                        Manage the product catalog and product information.
                    </p>
                </div>

                <div class="rounded-lg border border-stockcore-border bg-stockcore-surface p-5">
                    <h3 class="font-semibold">Categories</h3>
                    <p class="mt-2 text-sm leading-6 text-stockcore-muted">
                        Organize products and browse products by category.
                    </p>
                </div>

                <div class="rounded-lg border border-stockcore-border bg-stockcore-surface p-5">
                    <h3 class="font-semibold">Suppliers</h3>
                    <p class="mt-2 text-sm leading-6 text-stockcore-muted">
                        Manage suppliers and their relationships with products.
                    </p>
                </div>

                <div class="rounded-lg border border-stockcore-border bg-stockcore-surface p-5">
                    <h3 class="font-semibold">Inventory</h3>
                    <p class="mt-2 text-sm leading-6 text-stockcore-muted">
                        Track current stock and configure minimum and maximum levels.
                    </p>
                </div>

                <div class="rounded-lg border border-stockcore-border bg-stockcore-surface p-5">
                    <h3 class="font-semibold">Stock Movements</h3>
                    <p class="mt-2 text-sm leading-6 text-stockcore-muted">
                        Record entries, exits and adjustments with full stock traceability.
                    </p>
                </div>

                <div class="rounded-lg border border-stockcore-border bg-stockcore-surface p-5">
                    <h3 class="font-semibold">Customers</h3>
                    <p class="mt-2 text-sm leading-6 text-stockcore-muted">
                        Manage customer contact and identification information.
                    </p>
                </div>

            </div>
        </section>

        <section class="border-t border-stockcore-border py-10">
            <div class="mb-6">
                <p class="font-mono text-xs uppercase tracking-wider text-stockcore-muted">
                    Quick Start
                </p>

                <h2 class="mt-2 text-xl font-semibold">
                    Start testing the API
                </h2>

                <p class="mt-2 text-sm text-stockcore-muted">
                    The Postman collection is already configured for authentication and API requests.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="flex gap-4 rounded-lg border border-stockcore-border bg-stockcore-surface p-5">
                    <span class="font-mono text-sm text-stockcore-green">01</span>

                    <div>
                        <h3 class="font-semibold">Import the collection</h3>
                        <p class="mt-2 text-sm leading-6 text-stockcore-muted">
                            Import the StockCoreAPI collection included in the repository.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 rounded-lg border border-stockcore-border bg-stockcore-surface p-5">
                    <span class="font-mono text-sm text-stockcore-green">02</span>

                    <div>
                        <h3 class="font-semibold">Set the base URL</h3>
                        <p class="mt-2 text-sm leading-6 text-stockcore-muted">
                            Set <code class="font-mono text-stockcore-green">
                                @verbatim{{ base_url }}@endverbatim
                            </code>
                            to your local API URL.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 rounded-lg border border-stockcore-border bg-stockcore-surface p-5">
                    <span class="font-mono text-sm text-stockcore-green">03</span>

                    <div>
                        <h3 class="font-semibold">Authenticate</h3>
                        <p class="mt-2 text-sm leading-6 text-stockcore-muted">
                            Run <code class="font-mono text-stockcore-green">Auth &gt; Login</code>
                            using the included test user.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 rounded-lg border border-stockcore-border bg-stockcore-surface p-5">
                    <span class="font-mono text-sm text-stockcore-green">04</span>

                    <div>
                        <h3 class="font-semibold">Start testing</h3>
                        <p class="mt-2 text-sm leading-6 text-stockcore-muted">
                            The authentication token is stored automatically. No additional setup is required.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="border-t border-stockcore-border py-8">
            <div
                class="flex flex-col gap-3 text-sm text-stockcore-muted sm:flex-row sm:items-center sm:justify-between">
                <p>
                    StockCore API · Laravel 13
                </p>

                <p class="font-mono text-xs">
                    Built by Jose Carlos Oñate
                </p>
            </div>
        </footer>

    </main>
</body>

</html>
