<template>
    <div class="w-full">
        <Modal
            variant="alert"
            :dialog-header="{
                show: true,
                title: { content: 'Order Details' },
                description: {
                    content: ' Invoice: ' + (props.order?.invoice ?? ''),
                    attributes: {
                        class: 'pl-4',
                    },
                },

                attributes: {
                    class: 'bg-primary p-4 gap-0  text-primary-foreground',
                },
            }"
            :dialog-content="{
                attributes: {
                    class: 'p-0  overflow-hidden ',
                    close: {
                        attributes: {
                            class: 'text-primary-foreground',
                        },
                    },
                },
            }"
            :dialog-body="{
                attributes: {
                    class: 'pl-4 pr-1.5 pb-6',
                },
            }"
            :dialog-config="{
                useTrigger: false,
                key:'showInvoice',
                childCount: 1,
            }"
            :dialog-footer="{ show: false }"
            ref="dialog"
        >
            <template #dialog-trigger>
                <slot />
            </template>

            <template #dialog-body>
                <!-- TABLE -->
                <div class="space-y-1 text-sm">
                    <p><b>Purchaser:</b> {{ props.order?.purchaser }}</p>
                    <p>
                        <b>Distributor:</b>
                        {{ props.order?.distributor ?? "-" }}
                    </p>
                    <p><b>Order Date:</b> {{ props.order?.order_date }}</p>
                    <p>
                        <b>Order Total:</b> ${{
                            props.order?.order_total?.toFixed(2)
                        }}
                    </p>
                    <p>
                        <b>Commission:</b> ${{
                            props.order?.commission?.toFixed(2)
                        }}
                    </p>
                </div>
                <h3 class="mt-8 mb-3 font-semibold text-base text-slate-700">
                    Order Items
                </h3>

                <ScrollArea>
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
                                            'SKU',
                                            'Name',
                                            'Price',
                                            'Qty',
                                            'Total',
                                        ]"
                                        :key="i"
                                        scope="col"
                                        class="px-6 py-3 font-medium text-primary-foreground"
                                        >{{ heading }}</TableHead
                                    >
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <template v-if="(props.items || [])?.length !== 0">
                                    <TableRow
                                        v-for="row in props.items"
                                        :key="row?.invoice"
                                        class="odd:bg-secondary even:bg-background border-b border-muted hover:bg-secondary/80 w-full"
                                    >
                                        <TableCell
                                            class="px-6 py-4 font-medium text-heading whitespace-nowrap w-fit"
                                            >{{ row?.sku }}</TableCell
                                        >
                                        <TableCell class="px-6 py-4">{{
                                            row?.product_name
                                        }}</TableCell>
                                        <TableCell
                                            class="px-6 py-4 font-bold text-green-700"
                                            >${{
                                                (row?.price)?.toFixed(2)
                                            }}</TableCell
                                        >
                                        <TableCell class="px-6 py-4">
                                            {{ row?.quantity }}
                                        </TableCell>
                                        <TableCell class="px-6 py-4"
                                            >${{
                                                row?.total?.toFixed(2)
                                            }}</TableCell
                                        >
                                    </TableRow></template
                                >

                                <template v-else>
                                    <TableEmpty colspan="5"
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
                    <ScrollBar orientation="horizontal" />
                </ScrollArea>
            </template>
        </Modal>
    </div>
</template>

<script setup lang="ts">
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
import Modal from "../shared/Modal.vue";
import { ScrollArea, ScrollBar } from "../ui/scroll-area";
import { DatabaseZap } from "lucide-vue-next";

const props = defineProps<{
    order: any | null;
    items: any[] | [];
}>();
</script>
