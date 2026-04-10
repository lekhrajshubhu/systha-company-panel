import { VuetifyTiptap, VuetifyViewer, createVuetifyProTipTap } from "vuetify-pro-tiptap";
import {
    BaseKit,
    Bold,
    Italic,
    Underline,
    Strike,
    BulletList,
    OrderedList,
    Link,
    History,
} from "vuetify-pro-tiptap";

export const vuetifyProTipTap = createVuetifyProTipTap({
    lang: "en",
    fallbackLang: "en",
    components: {
        VuetifyTiptap,
        VuetifyViewer,
    },
    extensions: [
        BaseKit.configure({
            placeholder: {
                placeholder: "Enter some text...",
            },
        }),
        Bold,
        Italic,
        Underline,
        Strike,
        BulletList,
        OrderedList,
        Link,
        History,
    ],
});
