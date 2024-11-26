import hljs from "highlight.js";
import MarkdownIt from "markdown-it";

export const debounce = (callback, delay = 0) => {
  let prevId = undefined;
  return (...params) => {
    if (prevId) {
      clearTimeout(prevId);
    }

    prevId = setTimeout(callback, delay, ...params);
  };
};

export const markdownIt = () => {
  let md = null;
  return {
    getInstance() {
      if (!md) {
        md = MarkdownIt({
          highlight: function (code, lang) {
            if (lang && hljs.getLanguage(lang)) {
              try {
                const highlighted = hljs.highlight(code, { language: lang }).value;
                return `
                    <div class="code-block relative">
                      <pre><code class="hljs ${lang}">${highlighted}</code></pre>
                      <button class="copy-btn absolute top-1 right-1" 
                      onclick="navigator.clipboard.writeText('${code.replace(
                        /'/g,
                        "\\'"
                      )}')">
                        Copy
                      </button>
                    </div>
                  `;
              } catch (error) {
                console.log(error);
              }
            }
            return (
              '<pre><code class="hljs">' + md.utils.escapeHtml(str) + "</code></pre>"
            );
          },
        });
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
  const hour = Math.floor(minute / 24);
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
