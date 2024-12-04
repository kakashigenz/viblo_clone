import { Marked } from "marked";
import { markedHighlight } from "marked-highlight";
import hljs from "highlight.js";
import { gfmHeadingId } from "marked-gfm-heading-id";

export const debounce = (callback, delay = 0) => {
  let prevId = undefined;
  return (...params) => {
    if (prevId) {
      clearTimeout(prevId);
    }

    prevId = setTimeout(callback, delay, ...params);
  };
};

export const escapeHtml = (text) => {
  const map = {
    "&": "&amp;",
    '"': "&quot;",
    "'": "&#39;",
    "<": "&lt;",
    ">": "&gt;",
  };
  return text.replace(/[&"'<>]/g, (char) => map[char] || char);
};

export const marked = () => {
  let md = null;
  return {
    getInstance() {
      if (!md) {
        md = new Marked(
          markedHighlight({
            emptyLangClass: "hljs",
            langPrefix: "hljs language-",
            highlight(code, lang, info) {
              const language = hljs.getLanguage(lang) ? lang : "plaintext";
              const content = hljs.highlight(code, { language }).value;
              return `${content}<button class="bg-black bg-opacity-40 hover:bg-opacity-30 text-gray-200 rounded-md flex justify-center items-center absolute right-4 top-4">
                <i class="pi pi-clipboard copy-code-btn p-1" data-code="${escapeHtml(
                  code
                )}" style="font-size:16px"></i>
              </button>`;
            },
          })
        );
        md.use(gfmHeadingId());
      }
      return md;
    },
  };
};

export const getURlAvatar = (user) => {
  return (
    user.avatar ?? `https://placehold.co/45x45/green/FFF?text=${user.name.slice(0, 1)}`
  );
};

export const getFormatedTime = (timestamp, locate = "vi-VN") => {
  let value;
  const diff = (Date.now() - new Date(timestamp).getTime()) / 1000;
  const minute = Math.floor(diff / 60);
  const hour = Math.floor(minute / 60);
  const dtf = new Intl.DateTimeFormat(locate, {
    dateStyle: "medium",
    timeStyle: "short",
    hour12: true,
  });
  const rtf = new Intl.RelativeTimeFormat(locate, { numeric: "auto" });

  if (hour > 24) {
    return dtf.format(new Date(timestamp));
  }
  if (minute > 60) {
    return rtf.format(-hour, "hour");
  }
  return rtf.format(-minute, "minute");
};
