class IMage{constructor(e){this.base64=e,this.loaded=!1}load(){return new Promise(e=>{this.loaded&&e(this.image),this.image=new Image,this.image.src=this.base64,this.image.onload=()=>{this.loaded=!0,e(this.image)}})}square(e){return this.rect(e,e)}rect(g,I){return g=parseInt(g,10),I=parseInt(I,10),new Promise(o=>{this.load().then(e=>{let t=e.width,a=e.height;var r=t/a,s=g/I,i=g,h=I;let n=0,d=0;1<r&&1<s||r<1&&s<1?s<r?(t=a*s,(n=(e.width-t)/2)<0&&(n=0)):(a=t/s,(d=(e.height-a)/2)<0&&(d=0)):1<r&&s<1?(t=a*s,(n=(e.width-t)/2)<0&&(n=0)):(a=t/s,(d=(e.height-a)/2)<0&&(d=0)),this.render(n,d,t,a,0,0,i,h).then(e=>{o(e)})})})}#draw(e,t,a,r,s,i,h,n,d){var o=document.createElement("canvas");return o.width=n,o.height=d,o.getContext("2d").drawImage(e,t,a,r,s,i,h,n,d),o.toDataURL()}render(a,r,s,i,h,n,d,o){return new Promise(t=>{this.load().then(e=>{t(this.#draw(e,parseInt(a,10),parseInt(r,10),parseInt(s,10),parseInt(i,10),parseInt(h,10),parseInt(n,10),parseInt(d,10),parseInt(o,10)))})})}}