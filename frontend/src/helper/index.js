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
