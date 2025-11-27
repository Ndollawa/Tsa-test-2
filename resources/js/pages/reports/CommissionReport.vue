<template>
    <MainLayout>
        <Head title="Commission Report">
            <link rel="preconnect" href="https://rsms.me/" />
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        </Head>

        <div class="px-10 py-14">
            <h1 class="text-3xl font-bold tracking-wide mb-8 text-slate-700">
                Commission Report
            </h1>

            <!-- FILTERS -->
            <Card class="mb-10 max-w-xl">
                <CardContent class="py-6 space-y-4">
                    <div class="grid grid-cols-8 gap-6 w-full">
                        <div class="col-span-5">
                            <Label class="block text-sm font-medium mb-1"
                                >Distributor</Label
                            >
                            <Input
                                v-model="distributor"
                                placeholder="ID / First name / Last name"
                            />
                        </div>

                        <div class="col-span-3">
                            <Label class="block text-sm font-medium mb-1"
                                >Invoice</Label
                            >
                            <Input v-model="invoice" placeholder="Invoice #" />
                        </div>

                        <div class="col-span-4">
                            <Label class="block text-sm font-medium mb-1"
                                >Date From</Label
                            >
                            <Popover v-slot="{ close }">
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        :class="
                                            cn(
                                                'w-[240px] justify-start text-left font-normal',
                                                !dateFromCalendar &&
                                                    'text-muted-foreground',
                                            )
                                        "
                                    >
                                        <CalendarIcon />
                                        {{
                                            dateFromCalendar
                                                ? df.format(
                                                      dateFromCalendar?.toDate(
                                                          getLocalTimeZone(),
                                                      ),
                                                  )
                                                : "Pick a date"
                                        }}
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent
                                    class="w-auto p-0"
                                    align="start"
                                >
                                    <Calendar
                                        v-model="dateFromCalendar"
                                        :default-placeholder="
                                            defaultPlaceholder
                                        "
                                        layout="month-and-year"
                                        initial-focus
                                        @update:model-value="close"
                                    />
                                </PopoverContent>
                            </Popover>
                        </div>

                        <div class="col-span-4">
                            <Label class="block text-sm font-medium mb-1"
                                >Date To</Label
                            >

                            <Popover v-slot="{ close }">
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        :class="
                                            cn(
                                                'w-[240px] justify-start text-left font-normal',
                                                !dateToCalendar &&
                                                    'text-muted-foreground',
                                            )
                                        "
                                    >
                                        <CalendarIcon />
                                        {{
                                            dateToCalendar
                                                ? df.format(
                                                      dateToCalendar?.toDate(
                                                          getLocalTimeZone(),
                                                      ),
                                                  )
                                                : "Pick a date"
                                        }}
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent
                                    class="w-auto p-0"
                                    align="start"
                                >
                                    <Calendar
                                        v-model="dateToCalendar"
                                        :default-placeholder="
                                            defaultPlaceholder
                                        "
                                        layout="month-and-year"
                                        initial-focus
                                        :min="dateFrom"
                                        @update:model-value="close"
                                    />
                                </PopoverContent>
                            </Popover>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-2 justify-between w-full">
                        <div class="text-sm text-muted-foreground">
                            Total Record(s):
                            <span class="font-medium">{{
                                props?.results?.total ?? 0
                            }}</span>
                        </div>
                        <div class="flex gap-5 items-center">
                            <Button
                                variant="outline"
                                size="sm"
                                @click="resetFilters"
                                class="text-sm px-8 cursor-pointer"
                            >
                                Reset
                            </Button>
                            <Button
                                variant="default"
                                size="sm"
                                @click="applyFilters"
                                class="text-sm px-8 bg-primary/80 cursor-pointer"
                            >
                                Apply Filters
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
            <!-- TABLE -->
            <InvoiceModal :order="currentOrder" :items="currentItems" />

            <Card>
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
                                            'Invoice',
                                            'Purchaser',
                                            'Distributor',
                                            'Referred Dist.',
                                            'Order Date',
                                            'Percentage',
                                            'Order Total',
                                            'Commission',
                                            'Action',
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
                                        :key="row?.invoice"
                                        class="odd:bg-secondary even:bg-background border-b border-muted hover:bg-secondary/80 w-full"
                                    >
                                        <TableCell
                                            class="px-6 py-4 font-medium text-heading whitespace-nowrap w-fit"
                                            >{{ row?.invoice }}</TableCell
                                        >
                                        <TableCell class="px-6 py-4">{{
                                            row?.purchaser
                                        }}</TableCell>
                                        <TableCell class="px-6 py-4">{{
                                            row?.distributor ?? "-"
                                        }}</TableCell>
                                        <TableCell class="px-6 py-4">{{
                                            row?.referred_distributors
                                        }}</TableCell>
                                        <TableCell class="px-6 py-4">{{
                                            row?.order_date
                                        }}</TableCell>
                                        <TableCell class="px-6 py-4"
                                            >{{
                                                (row?.percentage * 100).toFixed(
                                                    0,
                                                )
                                            }}%</TableCell
                                        >
                                        <TableCell class="px-6 py-4"
                                            >${{
                                                row?.order_total.toFixed(2)
                                            }}</TableCell
                                        >
                                        <TableCell
                                            class="px-6 py-4 font-bold text-green-700"
                                        >
                                            ${{ row?.commission.toFixed(2) }}
                                        </TableCell>
                                        <TableCell class="px-6 py-4">
                                            <div class="">
                                                <Button
                                                    variant="link"
                                                    type="button"
                                                    class="font-medium cursor-pointer text-blue-600 hover:underline"
                                                    @click="
                                                        openDetails(
                                                            row.order_id,
                                                        )
                                                    "
                                                    >View Invoice</Button
                                                >
                                            </div>
                                        </TableCell>
                                    </TableRow></template
                                >

                                <template v-else>
                                    <TableEmpty colspan="9"
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
                                            :is-active="
                                                Number(link.label) ===
                                                props?.results?.current_page
                                            "
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
import type { DateValue } from "@internationalized/date";
import {
    DateFormatter,
    getLocalTimeZone,
    parseDate,
    today,
} from "@internationalized/date";

import { Button, buttonVariants } from "@/components/ui/button";
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
import { cn } from "@/lib/utils";
import { Head, router, usePage } from "@inertiajs/vue3";
import { CalendarIcon, DatabaseZap } from "lucide-vue-next";
import { computed, onMounted, Ref, ref, toValue, watch } from "vue";
import { useCommissionReportAPI } from "@/composables/api/commission-report";
import { useSearchParams } from "@/composables/useSearchParams";
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
import { clean } from "@/app/helpers";
import { usePersistentDialog } from "@/composables/usePersistentDialog";
import InvoiceModal from "@/components/pages/InvoiceModal.vue";
import MainLayout from "@/layouts/MainLayout.vue";
import { useShortPagination } from "@/composables";
import axios from "axios";

// shadcn components

const defaultPlaceholder = today(getLocalTimeZone());

const props = defineProps({
    filters: Object,
    results: Object,
    per_page: Number,
});

const page = usePage()

const shortLinks = useShortPagination(props.results, 5);

// ---------- DATE PICKER SETUP ----------
const df = new DateFormatter("en-US", { dateStyle: "long" });

const dateFromCalendar = ref<DateValue | null>(
    props.filters?.date_from ? parseDate(props.filters.date_from) : null,
);

const dateToCalendar = ref<DateValue | null>(
    props.filters?.date_to ? parseDate(props.filters.date_to) : null,
);

// Convert to YYYY-MM-DD for URL filters
const dateFrom = computed(() =>
    dateFromCalendar.value ? dateFromCalendar.value.toString() : "",
);

const dateTo = computed(() =>
    dateToCalendar.value ? dateToCalendar.value.toString() : "",
);

// ---------------------
// FORM STATE
// ---------------------
const distributor = ref(props.filters?.distributor ?? "");
const invoice = ref(props.filters?.invoice ?? "");
const perPage = ref(String(props.per_page ?? 10));

// ---------------------
// APPLY FILTERS
// ---------------------
function applyFilters() {
    const query = clean({
        distributor: distributor.value,
        invoice: invoice.value,
        date_from: dateFrom.value,
        date_to: dateTo.value,
        per_page: perPage.value,
        page: 1,
    });

    router.get(route("commission-report"), query, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

// ---------------------
// RESET FILTERS
// ---------------------
function resetFilters() {
    distributor.value = undefined;
    invoice.value = undefined;
    dateFromCalendar.value = null;
    dateToCalendar.value = null;
    const query = clean({
        // per_page: perPage.value,
        // page: 1,
    });

    router.get(route("commission-report"), query, {
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

// ---------------------
// INVIOCE
// ---------------------
const { openWith, closeModal } = usePersistentDialog("showInvoice")

const currentOrder = ref(null)
const currentItems = ref([])

async function openDetails(orderId:string) {
    const args = { orderId }

    openWith(String(orderId));
    const { data } = await axios.post(route('order-details'), { orderId });
    console.log(data)
currentOrder.value = data?.detail?.order;
currentItems.value = data?.detail?.items;

}


// Watch query param
onMounted(
  () => {
    const params = new URLSearchParams(page.url.split("?")[1] ?? "")
    const inv = params.get("inv")
    if (inv) {
      openDetails(inv)
    }
  },
)

</script>
