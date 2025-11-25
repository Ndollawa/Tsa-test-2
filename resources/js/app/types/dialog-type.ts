import { persistedDialogConst } from "../constants";

export type UIElement = {
    content?: string | object; // The content of the UI element (can be a string or a component)
    attributes?: Record<string, any>; // Optional attributes for the element (e.g., class, style, etc.)
};

export interface DialogInterface {
    variant: "dialog" | "alert";
    dialogTrigger?: { show: boolean } & UIElement;
    dialogContent?: UIElement;
    dialogHeader?: {
        show: boolean;
        title?: UIElement;
        description?: UIElement;
    } & UIElement;
    dialogBody?: UIElement;
    dialogFooter?: { show: boolean } & UIElement;
    dialogClose?: { show: boolean } & UIElement;
    dialogConfig?: {
        useTrigger: boolean;
        key?: (typeof persistedDialogConst)[keyof typeof persistedDialogConst];
        childCount: number;
    };
}
