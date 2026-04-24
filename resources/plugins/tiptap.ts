import { VuetifyTiptap, VuetifyViewer, createVuetifyProTipTap, defaultBubbleList } from "vuetify-pro-tiptap";
import {
    BaseKit,
    Blockquote,
    Bold,
    BulletList,
    Clear,
    // Code,
    CodeBlock,
    Color,
    FontFamily,
    FontSize,
    // Fullscreen,
    Heading,
    Highlight,
    History,
    Image,
    Indent,
    Italic,
    Link,
    OrderedList,
    Strike,
    SubAndSuperScript,
    Table,
    TaskList,
    TextAlign,
    Underline,
    Video,
    HorizontalRule,
} from "vuetify-pro-tiptap";

const fileToBase64 = (file: File): Promise<string> =>
    new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = () => {
            const result = reader.result;
            if (typeof result === "string") {
                resolve(result);
                return;
            }
            reject(new Error("Failed to convert image to base64."));
        };

        reader.onerror = () => {
            reject(reader.error ?? new Error("Image read failed."));
        };

        reader.readAsDataURL(file);
    });

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
            bubble: {
                list: {
                    text: [
                        "bold",
                        "italic",
                        "underline",
                        "strike",
                        "divider",
                        // "code",
                        "codeBlock",
                        "divider",
                        "color",
                        "highlight",
                        "textAlign",
                        "divider",
                        "link",
                    ],
                },
                defaultBubbleList: (editor) => defaultBubbleList(editor),
            },
        }),
        Bold,
        Italic,
        Underline,
        Strike,
        // Code.configure({
        //     divider: true,
        // }),
        Heading,
        TextAlign,
        FontFamily,
        FontSize,
        Color,
        Highlight.configure({ divider: true }),
        SubAndSuperScript.configure({ divider: true }),
        Clear.configure({ divider: true }),
        BulletList,
        OrderedList,
        TaskList,
        Indent.configure({ divider: true }),
        Link,
        Image.configure({
            divider: true,
            upload(file: File) {
                return fileToBase64(file);
            },
        }),
        // Video,
        Table.configure({ divider: true }),
        Blockquote,
        HorizontalRule,
        CodeBlock.configure({
            divider: true,
            HTMLAttributes: {
                class: "vpt-code-block",
            },
        }),
        History.configure({ divider: true }),
        // Fullscreen,
    ],
});
