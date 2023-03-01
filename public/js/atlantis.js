export default class Atlantis{#handlers=new Map;#lazyimageObserver=this.enter(t=>{var e;t.dataset?.loaded||((e=new Image).onload=function(){t.dataset.hasOwnProperty("src")?t.src=this.src:t.dataset.hasOwnProperty("bg")&&this.css(t,{"background-image":`url(${this.src})`}),t.dataset.loaded=!0},t.dataset.hasOwnProperty("src")?e.src=t.dataset.src:t.dataset.hasOwnProperty("bg")&&(e.src=t.dataset.bg))});constructor({log:t=!1}={}){this.log=t,this.uuid=this.getUUID()||this.setUUID(),new MutationObserver(t=>{this.#removeEventListeners(t[0]?.removedNodes)}).observe(document,{childList:!0,subtree:!0})}#removeEventListeners(t){return t instanceof NodeList&&t.forEach(t=>{1==t.nodeType&&(this.off(t),this.#removeEventListeners(t.childNodes))}),this}on(t,e,s,a=!1){var i=this.#handlers.get(e)||new Set;return t.addEventListener(e,s,a),i.add({element:t,handler:s,capture:a}),this.#handlers.set(e,i),this}off(t,e=void 0,s=void 0){for(var[a,i]of this.#handlers)if(a===e){for(const n of i)if(n.handler===s&&(t.removeEventListener(a,n.handler,n.capture),i.delete(n),s))return this;if(e)return this}return this}tag(t,{className:e=null,classList:s=[],css:a={},data:i={},attr:n={},text:r=null,html:o=null,append:c=[],on:l={}}={}){const h=document.createElement(t);return e&&(h.className=e),s.length&&h.classList.add(...s),a&&this.css(h,a),i&&this.data(h,i),n&&this.attr(h,n),r?h.textContent=r:o&&(h.innerHTML=o),c?.length&&h.append(...c),Object.entries(l).forEach(([t,e])=>{this.on(h,t,e)}),h}css(s,t={}){return t instanceof Object&&Object.entries(t).forEach(([t,e])=>{s.style[t]=e}),this}data(s,t={}){return Object.entries(t).forEach(([t,e])=>{s.dataset[t]=e}),this}attr(s,t={}){return Object.entries(t).forEach(([t,e])=>{s.setAttribute(t,e)}),this}handleEvent(t){"change"===t.type&&this.setArticleRating(t.target.closest("form[data-id]").dataset.id,t.target.value)}fetch(t,{method:e="POST",headers:s={Accept:"application/json"},body:a=void 0,success:i=function(){},failure:n=function(){}}={}){"string"!=typeof a&&a instanceof FormData==!1&&(a=JSON.stringify(a)),s={Accept:"application/json",...s},"string"==typeof a&&(s={...s,"Content-Length":a.length.toString()}),fetch(t,{method:e,headers:s,body:a}).then(t=>{if(!t.ok)return n(t.statusText),console.error("Atlantis.fetch():",t.status);"application/json"===s.Accept?t.json().then(t=>{this.log&&console.log("Atlantis.fetch():",t),i(t)}).catch(t=>{n(t),console.error("Atlantis.fetch():",t)}):t.text().then(t=>{this.log&&console.log("Atlantis.fetch():",t),i(t)}).catch(t=>{n(t),console.error("Atlantis.fetch():",t)})}).catch(t=>{n(t),console.error("Atlantis.fetch():",t)})}uniqueid(){return self.crypto.randomUUID()}toInt(t){return parseInt(t,10)}getCookie(t){t=document.cookie.match(new RegExp(`(?:^|; )${t.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g,"\\$1")}=([^;]*)`));return t?decodeURIComponent(t[1]):void 0}setCookie(t,e,s={}){s={path:"/",secure:!0,domain:window.location.hostname,samesite:"strict",expires:new Date(Date.now()+121e7).toUTCString(),...s};let a=encodeURIComponent(t)+"="+encodeURIComponent(e);Object.entries(s).forEach(([t,e])=>{a+=`; ${t}=`+(e||"")}),document.cookie=a}getUUID(t="atlantis_uuid"){return this.getCookie(t)}setUUID(t="atlantis_uuid"){this.setCookie(t,self.crypto.randomUUID())}dialog({title:t,message:e,buttons:s=[],onclose:a=function(){}}={}){var i="atlantis-dialog-"+this.uniqueid();const n=this.tag("dialog",{classList:["m-0","rounded","shadow-xl","fixed","md:w-96","w-full","top-1/2","left-1/2","transform","-translate-x-1/2","-translate-y-1/2"],attr:{id:i},on:{close:a}}),r=this.tag("div",{classList:["flex","items-center","gap-4"]});return s.forEach(t=>{var e=this.tag("button",{attr:{type:"button"},className:t?.class||"bg-sky-600 text-white hover:bg-sky-500 focus:bg-sky-500 active:bg-sky-500",classList:["inline-block","w-full","px-6","py-2.5","text-white","font-medium","text-xs","leading-tight","uppercase","rounded","shadow-md","hover:shadow-lg","focus:shadow-lg","focus:outline-none","focus:ring-0","active:shadow-lg","transition","duration-150","ease-in-out"],html:t?.text||"Button",on:{click:()=>{"function"==typeof t?.callback&&t?.callback(),n.close()}}});r.append(e)}),n.append(this.tag("form",{attr:{method:"dialog"},append:[this.tag("button",{classList:["absolute","top-4","right-4","ring-0","focus:ring-0","outline-none","text-gray-500"],append:[this.tag("i",{classList:["fas","fa-times","text-xl"],attr:{value:"cancel"}})]}),this.tag("span",{classList:["block","text-xl","font-medium","leading-normal","text-gray-800","pr-12","mb-4","truncate"],text:t}),this.tag("div",{classList:["mb-4"],html:e}),r]})),document.body.append(n),n}enter(s){return new IntersectionObserver((t,e)=>{t.forEach(t=>{t.isIntersecting&&s(t.target)})},{root:null,rootMargin:"0px",threshold:.1})}lazyimage(){var t=["[data-atlantis-lazy-image][data-src]","[data-atlantis-lazy-image][data-bg]"];this.#lazyimageObserver.takeRecords().forEach(t=>{this.#lazyimageObserver.unobserve(t.target)}),t.forEach(t=>{document.querySelectorAll(t).forEach(t=>{t.dataset?.loaded||this.#lazyimageObserver.observe(t)})})}lazyload(e,{url:s="",token:a="",page:i=1,next:n=1,last:r=1,limit:o=1,sort:c="",order:l="",search:h="",callback:d=function(){}}={}){const g=this.tag("div",{classList:["flex","justify-center","items-center"],attr:{"data-atlantis-lazyload-target":!0},append:[this.tag("div",{classList:["spinner-grow","inline-block","w-8","h-8","bg-sky-500","rounded-full","opacity-0"],attr:{role:"status"}})]}),u=(e.appendChild(g),this.enter(t=>{if(i==r)return this.log&&console.log("Atlantis.lazyload(): No more pages to load"),u.disconnect(),g.remove();this.fetch(s,{headers:{"Content-Type":"application/json","X-Csrf-Token":a},body:{page:i,next:n,last:r,limit:o,sort:c,order:l,search:h},success:t=>{g.insertAdjacentHTML("beforebegin",t.html),i=t.page,n=t.next,r=t.last,a=t.token,e.appendChild(g),d()}})}));u.observe(g)}lightbox(t,e="thumb_"){var s=this.tag("button",{classList:["absolute","top-4","right-4","ring-0","focus:ring-0","outline-none","text-gray-400","hover:text-gray-300","z-[1080]"],attr:{value:"cancel"},append:[this.tag("i",{classList:["fas","fa-times","text-2xl"]})],on:{click:function(t){c.classList.add("hidden"),document.body.classList.remove("overflow-hidden")}}}),a=this.tag("div",{classList:["w-full","h-full","absolute","top-0","left-0","z-[1040]","backdrop-blur-sm"]}),i=this.tag("button",{classList:["absolute","top-[calc(50%-(3.5rem))]","right-0","ring-0","w-14","h-14","m-0","flex","items-center","justify-center","focus:ring-0","outline-none","text-gray-400","hover:text-gray-300","z-[1080]","hover:bg-gray-800"],attr:{value:"next"},append:[this.tag("i",{classList:["fas","fa-chevron-right","text-2xl"]})],on:{click:()=>{var t=o.querySelector("[data-active]")?.nextElementSibling;t&&t.click()}}}),n=this.tag("button",{classList:["absolute","top-[calc(50%-(3.5rem))]","left-0","ring-0","w-14","h-14","m-0","flex","items-center","justify-center","focus:ring-0","outline-none","text-gray-400","hover:text-gray-300","z-[1080]","hover:bg-gray-800"],attr:{value:"prev"},append:[this.tag("i",{classList:["fas","fa-chevron-left","text-2xl"]})],on:{click:()=>{var t=o.querySelector("[data-active]")?.previousElementSibling;t&&t.click()}}});const r=this.tag("div",{classList:["h-[calc(100vh-(100px+3.5rem))]","flex","items-center","justify-center","m-14","mb-0","relative","z-[1080]"]}),o=this.tag("div",{classList:["h-[calc(100px-2rem)]","flex","items-center","gap-4","m-4","relative","z-[1080]"]}),c=this.tag("div",{classList:["hidden","fixed","top-0","left-0","w-screen","h-screen","z-[1040]","bg-black/75","transition-all","ease-in-out"],append:[a,s,i,n,r,o]}),l=(document.body.append(c),t=>{t.dataset.active=!0,t.classList.remove("brightness-50")}),h=t=>{delete t.dataset.active,t.classList.add("brightness-50")},d=()=>{const e=u(r.querySelector("img").src);o.querySelectorAll("img").forEach(t=>{(u(t.src)==e?l:h)(t)})},g=()=>{t.querySelectorAll("img").forEach(t=>{t=this.tag("img",{classList:["object-cover","w-auto","h-full","cursor-pointer"],attr:{width:t.width,height:t.height,src:t.src},on:{click:function(){var t=r.querySelector("img");t.src=u(this.src),t.width=this.width,t.height=this.height,d()}}});o.append(t)})};function u(t){return t.replace(new RegExp(e),"")}const f=t=>{t=t.target,r.innerHTML="",t=this.tag("img",{classList:["object-contain","mx-auto","w-full","h-full"],attr:{width:t.width,height:t.height,src:u(t.src)}});r.appendChild(t),0==o.children.length&&g(),d(),document.body.classList.add("overflow-hidden"),c.classList.remove("hidden"),o.scrollWidth>o.clientWidth?(o.classList.remove("justify-center"),o.classList.add("overflow-x-auto","justify-start")):o.classList.add("justify-center")};t.querySelectorAll("img").forEach(t=>{this.on(t,"click",f)})}}