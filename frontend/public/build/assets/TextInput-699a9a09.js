import{j as u,r as s}from"./app-6ded1f35.js";function f({message:e,className:t="",...r}){return e?u.jsx("p",{...r,className:"text-sm text-red-600 dark:text-red-400 "+t,children:e}):null}function x({value:e,className:t="",children:r,...n}){return u.jsx("label",{...n,className:"block font-medium text-sm text-gray-700 dark:text-gray-300 "+t,children:e||r})}const i=s.forwardRef(function({type:t="text",className:r="",isFocused:n=!1,...l},c){const o=s.useRef(null);return s.useImperativeHandle(c,()=>({focus:()=>{var a;return(a=o.current)==null?void 0:a.focus()}})),s.useEffect(()=>{var a;n&&((a=o.current)==null||a.focus())},[]),u.jsx("input",{...l,type:t,className:"border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm "+r,ref:o})});export{x as I,i as T,f as a};
