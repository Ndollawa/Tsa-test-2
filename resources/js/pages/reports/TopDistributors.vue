<template>
    <MainLayout>
        <Head title="Top Distributors Report">
            <link rel="preconnect" href="https://rsms.me/" />
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        </Head>

        <div class="px-10 py-14">
            <h1 class="text-3xl font-bold tracking-wide mb-8 text-slate-700">
                Top {{ per_page }} Distributors
            </h1>

            <Card class="max-w-3xl">
                <CardContent class="p-0">
                    <div
                        class="relative overflow-x-auto shadow-xs rounded-sm border"
                    >
                        <Table class="w-full text-sm text-left">
                            <TableHeader
                                class="text-sm text-primary-foreground bg-primary/80 border-collapse w-full"
                            >
                                <TableRow class="w-full">
                                    <TableHead
                                        v-for="(heading, i) in [
                                            'Rank',
                                            'Distributor\'s Name',
                                            'Total Sales',
                                        ]"
                                        :key="i"
                                        scope="col"
                                        class="px-6 py-3 font-medium text-primary-foreground"
                                        >{{ heading }}</TableHead
                                    >
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <template
                                    v-if="props?.results?.data?.length !== 0"
                                >
                                    <TableRow
                                        v-for="row in props?.results?.data"
                                        :key="row?.distributor_id"
                                        class="odd:bg-secondary even:bg-background border-b border-muted hover:bg-secondary/80 w-full"
                                    >
                                        <TableCell
                                            class="px-6 py-4 font-medium whitespace-nowrap"
                                        >
                                            {{ row.rank }}
                                        </TableCell>

                                        <TableCell class="px-6 py-4">
                                            {{ row.distributor_name }}
                                        </TableCell>

                                        <TableCell
                                            class="px-6 py-4 font-bold text-green-700"
                                        >
                                            ${{
                                                Number(row.total_sales).toFixed(
                                                    2,
                                                )
                                            }}
                                        </TableCell>
                                    </TableRow>
                                </template>

                                <template v-else>
                                    <TableEmpty colspan="3"
                                        ><div
                                            class="grid grid-cols-1 place-items-center w-full"
                                        >
                                            <Empty>
                                                <EmptyHeader>
                                                    <EmptyMedia variant="icon">
                                                        <DatabaseZap />
                                                    </EmptyMedia>
                                                    <EmptyTitle
                                                        >No Record
                                                        Found</EmptyTitle
                                                    >
                                                    <!-- <EmptyDescription>
                                                        // add description here
                                                    </EmptyDescription> -->
                                                </EmptyHeader>
                                            </Empty>
                                        </div></TableEmpty
                                    >
                                </template>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- PAGINATION -->
                    <div class="py-6 flex justify-between w-full px-10">
                        <!-- Per Page Selection -->
                        <div class="flex items-center gap-2 max-sm:flex-col">
                            <Select
                                v-model="perPage"
                                @update:model-value="applyFilters"
                            >
                                <SelectTrigger
                                    class="h-full gap-2 px-2 py-2 text-xs rounded-xxs w-fit"
                                >
                                    <SelectValue placeholder="Per page" />
                                </SelectTrigger>

                                <SelectContent>
                                    <SelectItem
                                        v-for="item in [
                                            5, 10, 20, 50, 100, 200,
                                        ]"
                                        :key="item"
                                        :value="String(item)"
                                    >
                                        {{ item }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                per page
                            </p>
                        </div>
                        <div class="">
                            <Pagination>
                                <PaginationContent>
                                    <PaginationPrevious
                                        :disabled="
                                            !props?.results?.prev_page_url
                                        "
                                        @click="
                                            go(props?.results?.prev_page_url)
                                        "
                                    />

                                    <template v-for="(link, i) in shortLinks">
                                        <PaginationItem
                                            v-if="
                                                link.url &&
                                                !isNaN(Number(link.label))
                                            "
                                            :key="link.url"
                                            :is-active="Number(link.label) === props?.results?.current_page"
                                            @click="go(link.url)"
                                        >
                                            <div
                                                class=""
                                                v-html="link.label"
                                            ></div>
                                        </PaginationItem>

                                        <PaginationEllipsis
                                            v-else-if="link.label === '...'"
                                            :index="4"
                                            :key="i"
                                        />
                                    </template>

                                    <PaginationNext
                                        :disabled="
                                            !props?.results?.next_page_url
                                        "
                                        @click="
                                            go(props?.results?.next_page_url)
                                        "
                                    />
                                </PaginationContent>
                            </Pagination>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </MainLayout>
</template>

<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from "@/components/ui/pagination";

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    TableEmpty,
} from "@/components/ui/table";
import {
    Empty,
    EmptyContent,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from "@/components/ui/empty";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import { cn } from "@/lib/utils";
import { Head, router, usePage } from "@inertiajs/vue3";
import { computed, onMounted, Ref, ref, toValue, watch } from "vue";
import {
    useSearchParams,
    usePersistentDialog,
    useShortPagination,
} from "@/composables/";

import { clean } from "@/app/helpers";
import InvoiceModal from "@/components/pages/InvoiceModal.vue";
import MainLayout from "@/layouts/MainLayout.vue";

// shadcn components

const props = defineProps({
    filters: Object,
    results: Object,
    per_page: Number,
});

const perPage = ref(String(props.per_page ?? 10));
const shortLinks = useShortPagination(props.results, 5);
// ---------------------
// APPLY FILTERS
// ---------------------
function applyFilters() {
    const query = clean({
        // distributor: distributor.value,
        per_page: perPage.value,
        page: 1,
    });

    router.get("/reports/top-distributors", query, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

// ---------------------
// PAGINATION
// ---------------------
function go(url: string) {
    if (!url) return;
    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}
</script>
