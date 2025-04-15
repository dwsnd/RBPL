<!DOCTYPE html>

<!-- This code was generated using AnimaApp.com. 
This code is a high-fidelity prototype.
Get developer-friendly React or HTML/CSS code for this project at: https://projects.animaapp.com?utm_source=hosted-code 
12/04/2025 13:37:48 -->

<html><head><meta charset="utf-8"><meta name="viewport" content="width=1440, maximum-scale=1.0"><link rel="shortcut icon" type="image/png" href="https://animaproject.s3.amazonaws.com/home/favicon.png"><meta name="og:type" content="website"><meta name="twitter:card" content="photo"><script id="anima-load-script" src="load.js"></script><script id="anima-hotspots-script" src="hotspots.js"></script><script id="anima-overrides-script" src="overrides.js"></script><script src="https://animaapp.s3.amazonaws.com/js/timeline.js"></script><style>
@import url("https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css");

@import url("https://fonts.googleapis.com/css?family=Poppins:500,700,600,400");

/* The following line is used to measure usage of this code. You can remove it if you want. */
@import url("https://px.animaapp.com/67fa6c090f1f023f61dbd330.67fa6c0a0f1f023f61dbd333.UXwFHYD.hch.png");


.screen textarea:focus,
.screen input:focus {
  outline: none;
}

.screen * {
  -webkit-font-smoothing: antialiased;
  box-sizing: border-box;
}

.screen div {
  -webkit-text-size-adjust: none;
}

.component-wrapper a {
  display: contents;
  pointer-events: auto;
  text-decoration: none;
}

.component-wrapper * {
  -webkit-font-smoothing: antialiased;
  box-sizing: border-box;
  pointer-events: none;
}

.component-wrapper a *,
.component-wrapper input,
.component-wrapper video,
.component-wrapper iframe {
  pointer-events: auto;
}

.component-wrapper.not-ready,
.component-wrapper.not-ready * {
  visibility: hidden !important;
}

.screen a {
  display: contents;
  text-decoration: none;
}

.full-width-a {
  width: 100%;
}

.full-height-a {
  height: 100%;
}

.container-center-vertical {
  align-items: center;
  display: flex;
  flex-direction: row;
  height: 100%;
  pointer-events: none;
}

.container-center-vertical > * {
  flex-shrink: 0;
  pointer-events: auto;
}

.container-center-horizontal {
  display: flex;
  flex-direction: row;
  justify-content: center;
  pointer-events: none;
  width: 100%;
}

.container-center-horizontal > * {
  flex-shrink: 0;
  pointer-events: auto;
}

.auto-animated div {
  --z-index: -1;
  opacity: 0;
  position: absolute;
}

.auto-animated input {
  --z-index: -1;
  opacity: 0;
  position: absolute;
}

.auto-animated .container-center-vertical,
.auto-animated .container-center-horizontal {
  opacity: 1;
}

.overlay-base {
  display: none;
  height: 100%;
  opacity: 0;
  position: fixed;
  top: 0;
  width: 100%;
}

.overlay-base.animate-appear {
  align-items: center;
  animation: reveal 0.3s ease-in-out 1 normal forwards;
  display: flex;
  flex-direction: column;
  justify-content: center;
  opacity: 0;
}

.overlay-base.animate-disappear {
  animation: reveal 0.3s ease-in-out 1 reverse forwards;
  display: block;
  opacity: 1;
  pointer-events: none;
}

.overlay-base.animate-disappear * {
  pointer-events: none;
}

@keyframes reveal {
  from { opacity: 0 }
 to { opacity: 1 }
}

.animate-nodelay {
  animation-delay: 0s;
}

.align-self-flex-start {
  align-self: flex-start;
}

.align-self-flex-end {
  align-self: flex-end;
}

.align-self-flex-center {
  align-self: flex-center;
}

.valign-text-middle {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.valign-text-bottom {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}

input:focus {
  outline: none;
}

.listeners-active,
.listeners-active * {
  pointer-events: auto;
}

.hidden,
.hidden * {
  pointer-events: none;
  visibility: hidden;
}

.smart-layers-pointers,
.smart-layers-pointers * {
  pointer-events: auto;
  visibility: visible;
}

.listeners-active-click,
.listeners-active-click * {
  cursor: pointer;
}

* {
  box-sizing: border-box;
}
:root { 
  --alto: #d9d9d9;
  --black: #00000099;
  --crusta: #f9892e;
  --defaultblack: #000000;
  --defaultwhite: #ffffff;
  --gray100: #f8f9fa;
  --gray600: #6c757d;
  --orange: #ff7837;
  --orange--500: #fd7e14;
 
  --font-size-l: 18px;
  --font-size-m: 16px;
  --font-size-s: 14px;
  --font-size-xl: 20px;
  --font-size-xxl: 23px;
  --font-size-xxxl: 24px;
  --font-size-xxxxl: 48px;
 
  --font-family-poppins: "Poppins", Helvetica;
}
.poppins-semi-bold-black-16px {
  color: var(--defaultblack);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-m);
  font-style: normal;
  font-weight: 600;
}

.poppins-normal-black-14px {
  color: var(--defaultblack);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-s);
  font-style: normal;
  font-weight: 400;
}

.poppins-medium-black-16px {
  color: var(--defaultblack);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-m);
  font-style: normal;
  font-weight: 500;
}

.poppins-semi-bold-orange-16px {
  color: var(--orange);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-m);
  font-style: normal;
  font-weight: 600;
}

.poppins-semi-bold-white-18px {
  color: var(--defaultwhite);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-l);
  font-style: normal;
  font-weight: 600;
}

.poppins-semi-bold-black-18px {
  color: var(--defaultblack);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-l);
  font-style: normal;
  font-weight: 600;
}

.poppins-normal-black-16px {
  color: var(--black);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-m);
  font-style: normal;
  font-weight: 400;
}

.poppins-semi-bold-black-23px {
  color: var(--defaultblack);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-xxl);
  font-style: normal;
  font-weight: 600;
}

.poppins-semi-bold-black-24px {
  color: var(--defaultblack);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-xxxl);
  font-style: normal;
  font-weight: 600;
}

:root {
}


.frame-236 {
  align-items: flex-start;
  background-color: transparent;
  display: inline-flex;
  gap: 8px;
  position: absolute;
}

.ling-ling-pet-shop {
  background-color: transparent;
  letter-spacing: 0.00px;
  line-height: normal;
  text-align: left;
}

.rectangle-589 {
  border-radius: 25px;
  height: 39px;
  left: 0px;
  position: absolute;
  top: 0px;
  width: 115px;
}

.shape {
  background-color: transparent;
  position: absolute;
}

.vector {
  background-color: transparent;
  position: absolute;
}
/* screen - homepage-user */

.homepage-user {
  background-color: var(--defaultwhite);
  height: 3240px;
  overflow: hidden;
  overflow-x: hidden;
  position: relative;
  width: 1440px;
}

.homepage-user .shape-ZrInLc {
  height: 180px;
  left: 256px;
  top: 20px;
  width: 179px;
}

.homepage-user .shape-YgNiYF {
  height: 157px;
  left: 344px;
  top: 589px;
  width: 162px;
}

.homepage-user .ling-ling-pet-shop-ZrInLc {
  height: auto;
  left: 71px;
  position: absolute;
  top: 218px;
  width: 165px;
}

.homepage-user .title-ZrInLc {
  background-color: transparent;
  color: var(--defaultblack);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-xxxxl);
  font-style: normal;
  font-weight: 700;
  height: auto;
  left: 69px;
  letter-spacing: 0.00px;
  line-height: normal;
  position: absolute;
  text-align: left;
  top: 249px;
  width: 606px;
}

.homepage-user .group-237-ZrInLc {
  background-color: transparent;
  cursor: pointer;
  height: 60px;
  left: 71px;
  position: absolute;
  top: 469px;
  width: 177px;
}

.homepage-user .rectangle-594-JXktt7 {
  background-color: var(--defaultblack);
  border-radius: 10px;
  height: 56px;
  left: 0px;
  position: absolute;
  top: 2px;
  width: 175px;
}

.homepage-user .mulai-belanja-JXktt7 {
  background-color: transparent;
  height: 60px;
  left: 24px;
  letter-spacing: 0.00px;
  line-height: normal;
  position: absolute;
  text-align: center;
  top: 0px;
  width: 127px;
}

.homepage-user .group-233-ZrInLc {
  background-color: transparent;
  height: 555px;
  left: 682px;
  position: absolute;
  top: 220px;
  width: 718px;
}

.homepage-user .vector-aLom1Z {
  height: 554px;
  left: 0px;
  top: 0px;
  width: 718px;
}

.homepage-user .vector-VB8ifW {
  height: 493px;
  left: 97px;
  top: 62px;
  width: 543px;
}

.homepage-user .patternpaws-aLom1Z {
  background-color: transparent;
  height: 478px;
  left: 91px;
  position: absolute;
  top: 24px;
  width: 479px;
}

.homepage-user .vector-Kp2uGo {
  height: 110px;
  left: 232px;
  top: 165px;
  width: 109px;
}

.homepage-user .view-cats-dogs-being-friends-3-ZrInLc {
  background-color: transparent;
  height: 548px;
  left: 688px;
  position: absolute;
  top: 227px;
  width: 725px;
}

.homepage-user .group-239-ZrInLc {
  background-color: transparent;
  height: 40px;
  left: 536px;
  position: absolute;
  top: 883px;
  width: 370px;
}

.homepage-user .layanan-kami-FyyQ4E {
  background-color: transparent;
  height: auto;
  left: 112px;
  letter-spacing: 0.00px;
  line-height: 20px;
  position: absolute;
  text-align: left;
  top: 10px;
  width: 255px;
}

.homepage-user .group-242-ZrInLc {
  background-color: transparent;
  height: 40px;
  left: 536px;
  position: absolute;
  top: 2174px;
  width: 391px;
}

.homepage-user .produk-kami-A35DWx {
  background-color: transparent;
  height: auto;
  left: 113px;
  letter-spacing: 0.00px;
  line-height: 20px;
  position: absolute;
  text-align: left;
  top: 12px;
  width: 276px;
}

.homepage-user .group-240-ZrInLc {
  background-color: transparent;
  height: 643px;
  left: 86px;
  position: absolute;
  top: 1469px;
  width: 567px;
}

.homepage-user .segera-hubungi-kami-ZrInLc {
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  background-color: transparent;
  color: var(--defaultblack);
  display: -webkit-box;
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-xxxl);
  font-style: normal;
  font-weight: 700;
  height: auto;
  left: 751px;
  letter-spacing: 0.00px;
  line-height: normal;
  overflow: hidden;
  position: absolute;
  text-align: left;
  text-overflow: ellipsis;
  top: 1620px;
  width: 587px;
}

.homepage-user .jika-hewan-kesayanga-ZrInLc {
  background-color: transparent;
  color: #000000cc;
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-m);
  font-style: normal;
  font-weight: 400;
  height: auto;
  left: 751px;
  letter-spacing: 0.00px;
  line-height: 25.6px;
  position: absolute;
  text-align: justify;
  top: 1703px;
  width: 546px;
}

.homepage-user .group-241-ZrInLc {
  background-color: transparent;
  cursor: pointer;
  height: 60px;
  left: 751px;
  position: absolute;
  top: 1792px;
  width: 201px;
}

.homepage-user .rectangle-595-qo96IC {
  background-color: var(--defaultblack);
  border-radius: 10px;
  height: 60px;
  left: 0px;
  position: absolute;
  top: 0px;
  width: 199px;
}

.homepage-user .memesan-jadwal-qo96IC {
  background-color: transparent;
  height: 59px;
  left: 14px;
  letter-spacing: 0.00px;
  line-height: normal;
  position: absolute;
  text-align: center;
  top: 0px;
  width: 172px;
}

.homepage-user .group-247-ZrInLc {
  background-color: transparent;
  height: 308px;
  left: 72px;
  position: absolute;
  top: 2267px;
  width: 1296px;
}

.homepage-user .group-243-njscM2 {
  background-color: transparent;
  cursor: pointer;
  height: 308px;
  left: 0px;
  position: absolute;
  top: 0px;
  width: 306px;
}

.homepage-user .img-FPEEcp {
  background-image: url(https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/img-5@2x.png);
}

.homepage-user .aksesoris-2GrMxn {
  background-color: transparent;
  letter-spacing: 0.00px;
  line-height: 20px;
  margin-top: -1.00px;
  position: relative;
  text-align: left;
  white-space: nowrap;
  width: fit-content;
}

.homepage-user .x84-produk-2GrMxn {
  background-color: transparent;
  letter-spacing: 0.00px;
  line-height: 20px;
  position: relative;
  text-align: left;
  white-space: nowrap;
  width: fit-content;
}

.homepage-user .group-244-njscM2 {
  background-color: transparent;
  cursor: pointer;
  height: 308px;
  left: 330px;
  position: absolute;
  top: 0px;
  width: 306px;
}

.homepage-user .img-OfZxCJ {
  background-image: url(https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/img-6@2x.png);
}

.homepage-user .makanan-lTIFQu {
  background-color: transparent;
  letter-spacing: 0.00px;
  line-height: 20px;
  margin-top: -1.00px;
  position: relative;
  text-align: left;
  white-space: nowrap;
  width: fit-content;
}

.homepage-user .x64-produk-lTIFQu {
  background-color: transparent;
  letter-spacing: 0.00px;
  line-height: 20px;
  position: relative;
  text-align: left;
  white-space: nowrap;
  width: fit-content;
}

.homepage-user .group-245-njscM2 {
  background-color: transparent;
  cursor: pointer;
  height: 308px;
  left: 660px;
  position: absolute;
  top: 0px;
  width: 306px;
}

.homepage-user .img-ud7Q2H {
  background-image: url(https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/img-7@2x.png);
}

.homepage-user .pasir-NLBZHF {
  background-color: transparent;
  letter-spacing: 0.00px;
  line-height: 20px;
  margin-top: -1.00px;
  position: relative;
  text-align: left;
  white-space: nowrap;
  width: fit-content;
}

.homepage-user .x22-produk-NLBZHF {
  background-color: transparent;
  letter-spacing: 0.00px;
  line-height: 20px;
  position: relative;
  text-align: left;
  white-space: nowrap;
  width: fit-content;
}

.homepage-user .group-246-njscM2 {
  background-color: transparent;
  cursor: pointer;
  height: 308px;
  left: 990px;
  position: absolute;
  top: 0px;
  width: 306px;
}

.homepage-user .img-RUe1xV {
  background-image: url(https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/img-4@2x.png);
}

.homepage-user .vitamin-gWMi1O {
  background-color: transparent;
  letter-spacing: 0.00px;
  line-height: 20px;
  margin-top: -1.00px;
  position: relative;
  text-align: left;
  white-space: nowrap;
  width: fit-content;
}

.homepage-user .x16-produk-gWMi1O {
  background-color: transparent;
  letter-spacing: 0.00px;
  line-height: 20px;
  position: relative;
  text-align: left;
  white-space: nowrap;
  width: fit-content;
}

.homepage-user .gold-bengal-cat-white-space-2-ZrInLc {
  background-color: transparent;
  height: 430px;
  left: 102px;
  position: absolute;
  top: 2621px;
  width: 1264px;
}

.homepage-user .select-services-homepage-ZrInLc {
  background-color: transparent;
  height: 280px;
  left: 124px;
  position: absolute;
  top: 978px;
  width: 1192px;
}

.homepage-user .rectangle-569-GhuA1X {
  background-color: var(--alto);
  border-radius: 20px;
  height: 280px;
  left: 0px;
  position: absolute;
  top: 0px;
  width: 280px;
}

.homepage-user .rectangle-570-GhuA1X {
  background-color: var(--alto);
  border-radius: 20px;
  height: 280px;
  left: 0px;
  position: absolute;
  top: 0px;
  width: 280px;
}

.homepage-user .rectangle-571-GhuA1X {
  background-color: var(--alto);
  border-radius: 20px;
  height: 280px;
  left: 304px;
  position: absolute;
  top: 0px;
  width: 280px;
}

.homepage-user .rectangle-572-GhuA1X {
  background-color: var(--alto);
  border-radius: 20px;
  height: 280px;
  left: 304px;
  position: absolute;
  top: 0px;
  width: 280px;
}

.homepage-user .rectangle-573-GhuA1X {
  background-color: var(--alto);
  border-radius: 20px;
  height: 280px;
  left: 608px;
  position: absolute;
  top: 0px;
  width: 280px;
}

.homepage-user .rectangle-574-GhuA1X {
  background-color: var(--alto);
  border-radius: 20px;
  height: 280px;
  left: 608px;
  position: absolute;
  top: 0px;
  width: 280px;
}

.homepage-user .rectangle-575-GhuA1X {
  background-color: var(--alto);
  border-radius: 20px;
  height: 280px;
  left: 912px;
  position: absolute;
  top: 0px;
  width: 280px;
}

.homepage-user .rectangle-576-GhuA1X {
  background-color: var(--alto);
  border-radius: 20px;
  height: 280px;
  left: 912px;
  position: absolute;
  top: 0px;
  width: 280px;
}

.homepage-user .shop-GhuA1X {
  height: 141px;
  left: 65px;
  object-fit: cover;
  top: 33px;
  width: 149px;
}

.homepage-user .dokter-GhuA1X {
  background-color: transparent;
  height: 141px;
  left: 988px;
  object-fit: cover;
  position: absolute;
  top: 33px;
  width: 128px;
}

.homepage-user .penitipan-GhuA1X {
  height: 141px;
  left: 677px;
  object-fit: cover;
  top: 33px;
  width: 142px;
}

.homepage-user .grooming-GhuA1X {
  background-color: transparent;
  height: 139px;
  left: 366px;
  object-fit: cover;
  position: absolute;
  top: 35px;
  width: 158px;
}

.homepage-user .shop-YGlnc9 {
  height: 78px;
  left: 0px;
  letter-spacing: 0.00px;
  line-height: 20px;
  text-align: center;
  top: 191px;
  width: 280px;
}

.homepage-user .perawatan-GhuA1X {
  background-color: transparent;
  height: 78px;
  left: 304px;
  letter-spacing: 0.00px;
  line-height: 20px;
  position: absolute;
  text-align: center;
  top: 191px;
  width: 280px;
}

.homepage-user .penitipan-YGlnc9 {
  height: 78px;
  left: 608px;
  letter-spacing: 0.00px;
  line-height: 20px;
  text-align: center;
  top: 191px;
  width: 280px;
}

.homepage-user .konsultasi-GhuA1X {
  background-color: transparent;
  height: 78px;
  left: 912px;
  letter-spacing: 0.00px;
  line-height: 20px;
  position: absolute;
  text-align: center;
  top: 191px;
  width: 280px;
}

.homepage-user .group-355-ZrInLc {
  background-color: transparent;
  height: 304px;
  left: 36px;
  position: absolute;
  top: 2936px;
  width: 1367px;
}

.homepage-user .group-251-05Lfeu {
  background-color: transparent;
  height: 215px;
  left: 0px;
  position: absolute;
  top: 0px;
  width: 351px;
}

.homepage-user .jl-parangtritis-km-6-sC2ElK {
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 9;
  background-color: transparent;
  display: -webkit-box;
  height: auto;
  left: 0px;
  letter-spacing: 0.00px;
  line-height: 18px;
  overflow: hidden;
  position: absolute;
  text-align: justify;
  text-overflow: ellipsis;
  top: 52px;
  width: 349px;
}

.homepage-user .frame-236-sC2ElK {
  left: 0px;
  top: 0px;
}

.homepage-user .group-i9UcuF {
  background-color: transparent;
  height: 22.9296875px;
  position: relative;
  width: 29.2716121673584px;
}

.homepage-user .ling-ling-pet-shop-i9UcuF {
  color: var(--defaultblack);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-xl);
  font-style: normal;
  font-weight: 700;
  margin-top: -1.00px;
  position: relative;
  width: fit-content;
}

.homepage-user .copyright-ling-ling-pet-shop-2025-05Lfeu {
  background-color: transparent;
  color: #00000080;
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-s);
  font-style: normal;
  font-weight: 400;
  height: auto;
  left: 1096px;
  letter-spacing: 0.00px;
  line-height: 26px;
  position: absolute;
  text-align: left;
  top: 183px;
  white-space: nowrap;
  width: auto;
}

.homepage-user .frame-252-05Lfeu {
  align-items: center;
  background-color: transparent;
  display: inline-flex;
  gap: 20px;
  left: 1248px;
  position: absolute;
  top: 130px;
}

.homepage-user .group-hSekho {
  background-color: transparent;
  cursor: pointer;
  height: 24px;
  position: relative;
  width: 23.993776321411133px;
}

.homepage-user .vector-NrLrX1 {
  height: 24px;
  left: 0px;
  top: 0px;
  width: 24px;
}

.homepage-user .vector-BCChzB {
  height: 19px;
  left: 7px;
  top: 5px;
  width: 10px;
}

.homepage-user .group-TiYhzI {
  background-color: transparent;
  cursor: pointer;
  height: 24px;
  position: relative;
  width: 23.99293327331543px;
}

.homepage-user .vector-xdlVZt {
  height: 24px;
  left: 0px;
  top: 0px;
  width: 24px;
}

.homepage-user .group-xdlVZt {
  background-color: transparent;
  height: 14px;
  left: 5px;
  position: absolute;
  top: 5px;
  width: 15px;
}

.homepage-user .group-SDsChZ {
  background-color: transparent;
  cursor: pointer;
  height: 24px;
  position: relative;
  width: 23.99293327331543px;
}

.homepage-user .vector-FmXUC4 {
  height: 24px;
  left: 0px;
  top: 0px;
  width: 24px;
}

.homepage-user .foursquare-FmXUC4 {
  background-color: transparent;
  height: 18px;
  left: 4px;
  position: absolute;
  top: 4px;
  width: 18px;
}

.homepage-user .senin-sabtu-jam-0900-05Lfeu {
  background-color: transparent;
  height: auto;
  left: 1197px;
  letter-spacing: 0.00px;
  line-height: 20px;
  position: absolute;
  text-align: right;
  top: 51px;
  width: auto;
}

.homepage-user .jam-buka-05Lfeu {
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 0;
  background-color: transparent;
  display: -webkit-box;
  height: auto;
  left: 1279px;
  letter-spacing: 0.00px;
  line-height: 52px;
  overflow: hidden;
  position: absolute;
  text-align: center;
  text-overflow: ellipsis;
  top: 3px;
  white-space: nowrap;
  width: 82px;
}

.homepage-user .mask-group-05Lfeu {
  background-color: transparent;
  height: 97px;
  left: 1173px;
  position: absolute;
  top: 207px;
  width: 187px;
}

.homepage-user .group-234-ZrInLc {
  background-color: transparent;
  height: 290px;
  left: 0px;
  position: fixed;
  top: 0px;
  width: 1440px;
}

.homepage-user .rectangle-588-rhKMtd {
  background-color: var(--defaultwhite);
  border-radius: 0px 0px 40px 40px;
  box-shadow: 0px 16px 12px #00000026;
  height: 102px;
  left: 0px;
  position: absolute;
  top: 0px;
  width: 1440px;
}

.homepage-user .group-235-rhKMtd {
  background-color: transparent;
  height: 265px;
  left: 503px;
  position: absolute;
  top: 25px;
  width: 401px;
}

.homepage-user .shop-OItU8H {
  height: 16px;
  left: 218px;
  letter-spacing: 0.00px;
  line-height: normal;
  text-align: center;
  top: 18px;
  white-space: nowrap;
  width: 72px;
}

.homepage-user .home-OItU8H {
  background-color: transparent;
  height: 16px;
  left: 0px;
  letter-spacing: 0.00px;
  line-height: normal;
  position: absolute;
  text-align: center;
  top: 18px;
  white-space: nowrap;
  width: 72px;
}

.homepage-user .vector-3-OItU8H {
  background-color: transparent;
  height: 2px;
  left: 2px;
  position: absolute;
  top: 41px;
  width: 68px;
}

.homepage-user .about-us-OItU8H {
  background-color: transparent;
  height: 16px;
  left: 304px;
  letter-spacing: 0.00px;
  line-height: normal;
  position: absolute;
  text-align: center;
  top: 18px;
  white-space: nowrap;
  width: 91px;
}

.homepage-user .dropdown-services-OItU8H {
  background-color: transparent;
  height: 265px;
  left: 82px;
  position: absolute;
  top: 0px;
  width: 136px;
}

.homepage-user .form-KcywN4 {
  align-items: center;
  background-color: var(--defaultwhite);
  border-radius: 8px;
  display: flex;
  gap: 8px;
  height: 52px;
  left: 0px;
  padding: 0px 12px;
  position: relative;
  top: 0px;
  width: 136px;
}

.homepage-user .text-wrap-126wtO {
  align-items: center;
  background-color: transparent;
  display: flex;
  flex: 1;
  flex-grow: 1;
  gap: 10px;
  overflow: hidden;
  position: relative;
}

.homepage-user .layanan-grBrVe {
  background-color: transparent;
  letter-spacing: 0.00px;
  line-height: normal;
  margin-top: -1.00px;
  position: relative;
  text-align: left;
  width: fit-content;
}

.homepage-user .keyboard-arrow-down-126wtO {
  background-color: transparent;
  height: 24px;
  position: relative;
  width: 24px;
}

.homepage-user .frame-236-rhKMtd {
  left: 50px;
  top: 35px;
}

.homepage-user .group-yjIvwP {
  background-color: transparent;
  height: 22.928802490234375px;
  position: relative;
  width: 29.2716121673584px;
}

.homepage-user .ling-ling-pet-shop-yjIvwP {
  color: var(--defaultblack);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-m);
  font-style: normal;
  font-weight: 700;
  margin-top: -1.00px;
  position: relative;
  width: fit-content;
}

.homepage-user .hover-signup-rhKMtd {
  background-color: transparent;
  height: 39px;
  left: 1275px;
  position: absolute;
  top: 32px;
  width: 115px;
}

.homepage-user .rectangle-589-hL6jmI {
  background-color: transparent;
  border: 2px solid;
  border-color: var(--orange);
}

.homepage-user .sign-up-hL6jmI {
  background-color: transparent;
  height: 12px;
  left: 15px;
  letter-spacing: 0.00px;
  line-height: normal;
  position: absolute;
  text-align: center;
  top: 13px;
  white-space: nowrap;
  width: 85px;
}

.homepage-user .hover-login-rhKMtd {
  background-color: transparent;
  height: 39px;
  left: 1149px;
  position: absolute;
  top: 32px;
  width: 115px;
}

.homepage-user .rectangle-589-Qrq86j {
  background-color: var(--orange);
}

.homepage-user .log-in-Qrq86j {
  background-color: transparent;
  color: var(--defaultwhite);
  font-family: var(--font-family-poppins);
  font-size: var(--font-size-m);
  font-style: normal;
  font-weight: 600;
  height: 26px;
  left: 25px;
  letter-spacing: 0.00px;
  line-height: normal;
  position: absolute;
  text-align: center;
  top: 6px;
  width: 67px;
}

.homepage-user .group-238 {
  background-color: transparent;
  height: 40px;
  left: 0px;
  position: absolute;
  top: 0px;
  width: 80px;
}

.homepage-user .img {
  background-color: transparent;
  background-position: 50% 50%;
  background-size: cover;
  border-radius: 15px 15px 0px 0px;
  height: 216px;
  left: 0px;
  overflow: hidden;
  position: absolute;
  top: 0px;
  width: 306px;
}

.homepage-user .penitipan {
  background-color: transparent;
  position: absolute;
}

.homepage-user .shop {
  background-color: transparent;
  position: absolute;
}

.homepage-user .text {
  align-items: center;
  background-color: transparent;
  display: inline-flex;
  flex: 0 0 auto;
  flex-direction: column;
  gap: 12px;
  position: relative;
}

.homepage-user .text-icon {
  align-items: flex-start;
  background-color: var(--gray100);
  border: 2px solid;
  border-color: var(--alto);
  border-radius: 0px 0px 15px 15px;
  display: flex;
  gap: 12px;
  justify-content: space-around;
  left: 0px;
  overflow: hidden;
  padding: 20px 16px;
  position: absolute;
  top: 216px;
  width: 306px;
}
</style></head><body style="margin: 0;background: #ffffff;"><input type="hidden" id="anPageName" name="page" value="homepage-user"><div class="container-center-horizontal"><div class="homepage-user screen " data-id="615:4878"><img class="shape-ZrInLc shape" data-id="615:4879" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/shape-2.svg" alt="Shape"><img class="shape-YgNiYF shape" data-id="615:4880" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/shape-3.svg" alt="Shape"><div class="ling-ling-pet-shop-ZrInLc ling-ling-pet-shop poppins-semi-bold-orange-16px" data-id="615:4881">Ling-Ling Pet Shop</div><h1 class="title-ZrInLc" data-id="615:4882">Belajar Praktis untuk Kebutuhan Hewan Peliharaan Anda</h1><a href="shop-user"><div class="group-237-ZrInLc" data-id="615:4883"><div class="rectangle-594-JXktt7" data-id="615:4884"></div><div class="mulai-belanja-JXktt7 valign-text-middle poppins-semi-bold-white-18px" data-id="615:4885">Mulai Belanja</div></div></a><div class="group-233-ZrInLc" data-id="615:4886"><img class="vector-aLom1Z vector" data-id="615:4887" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/vector-11.svg" alt="Vector"><img class="vector-VB8ifW vector" data-id="615:4888" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/vector-12.svg" alt="Vector"><img class="patternpaws-aLom1Z" data-id="615:4889" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/pattern-paws--1@2x.png" alt="pattern(paws)"><img class="vector-Kp2uGo vector" data-id="615:4935" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/vector-13.svg" alt="Vector"></div><img class="view-cats-dogs-being-friends-3-ZrInLc" data-id="615:4936" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/view-cats-dogs-being-friends-3.png" alt="view-cats-dogs-being-friends 3"><div class="group-239-ZrInLc" data-id="615:4937"><div class="layanan-kami-FyyQ4E poppins-semi-bold-black-24px" data-id="615:4938">Layanan Kami</div><img class="group-238" data-id="615:4939" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/group-238@2x.png" alt="Group 238"></div><div class="group-242-ZrInLc" data-id="615:4950"><div class="produk-kami-A35DWx poppins-semi-bold-black-24px" data-id="615:4951">Produk Kami</div><img class="group-238" data-id="615:4952" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/group-238-1@2x.png" alt="Group 238"></div><img class="group-240-ZrInLc" data-id="615:4963" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/group-240.png" alt="Group 240"><p class="segera-hubungi-kami-ZrInLc" data-id="615:5001">Segera Hubungi Kami dan Dapatkan Solusi Terbaik</p><p class="jika-hewan-kesayanga-ZrInLc" data-id="615:5002">Jika hewan kesayangan Anda sakit, butuh perawatan, atau layanan darurat, kami siap membantu. Dapatkan konsultasi, pengobatan, dan grooming dari tim profesional. Hubungi kami sekarang!</p><a href="https://wa.me/6283867056070" target="_blank"><div class="group-241-ZrInLc" data-id="615:5003"><div class="rectangle-595-qo96IC" data-id="615:5004"></div><div class="memesan-jadwal-qo96IC valign-text-middle poppins-semi-bold-white-18px" data-id="615:5005">Memesan Jadwal</div></div></a><div class="group-247-ZrInLc" data-id="615:5006"><a href="shop-user"><div class="group-243-njscM2" data-id="615:5007"><div class="img-FPEEcp img" data-id="615:5008-50f0219d-b2db-48d4-8e67-ab0800782ab2"></div><div class="text-icon" data-id="615:5009"><div class="text" data-id="615:5010"><div class="aksesoris-2GrMxn poppins-semi-bold-black-18px" data-id="615:5011">Aksesoris</div><div class="x84-produk-2GrMxn poppins-normal-black-16px" data-id="615:5012">84 produk</div></div></div></div></a><a href="shop-user"><div class="group-244-njscM2" data-id="615:5013"><div class="img-OfZxCJ img" data-id="615:5014-e5becda4-91eb-4a4d-b5a6-8679891dbc2a"></div><div class="text-icon" data-id="615:5015"><div class="text" data-id="615:5016"><div class="makanan-lTIFQu poppins-semi-bold-black-18px" data-id="615:5017">Makanan</div><div class="x64-produk-lTIFQu poppins-normal-black-16px" data-id="615:5018">64 produk</div></div></div></div></a><a href="shop-user"><div class="group-245-njscM2" data-id="615:5019"><div class="img-ud7Q2H img" data-id="615:5020-c8ab120d-f5ee-4c71-b49a-d47f71536e50"></div><div class="text-icon" data-id="615:5021"><div class="text" data-id="615:5022"><div class="pasir-NLBZHF poppins-semi-bold-black-18px" data-id="615:5023">Pasir</div><div class="x22-produk-NLBZHF poppins-normal-black-16px" data-id="615:5024">22 produk</div></div></div></div></a><a href="shop-user"><div class="group-246-njscM2" data-id="615:5025"><div class="img-RUe1xV img" data-id="615:5026-a66badcb-8445-4d4a-8912-7c97826a2f30"></div><div class="text-icon" data-id="615:5027"><div class="text" data-id="615:5028"><div class="vitamin-gWMi1O poppins-semi-bold-black-18px" data-id="615:5029">Vitamin</div><div class="x16-produk-gWMi1O poppins-normal-black-16px" data-id="615:5030">16 produk</div></div></div></div></a></div><img class="gold-bengal-cat-white-space-2-ZrInLc" data-id="615:5031" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/gold-bengal-cat-white-space-2.png" alt="gold-bengal-cat-white-space 2"><div class="select-services-homepage-ZrInLc" data-id="615:5032"><div class="rectangle-569-GhuA1X" data-id="I615:5032;225:3786"></div><div class="rectangle-570-GhuA1X" data-id="I615:5032;225:3787"></div><div class="rectangle-571-GhuA1X" data-id="I615:5032;225:3788"></div><div class="rectangle-572-GhuA1X" data-id="I615:5032;225:3789"></div><div class="rectangle-573-GhuA1X" data-id="I615:5032;225:3790"></div><div class="rectangle-574-GhuA1X" data-id="I615:5032;225:3791"></div><div class="rectangle-575-GhuA1X" data-id="I615:5032;225:3792"></div><div class="rectangle-576-GhuA1X" data-id="I615:5032;225:3793"></div><img class="shop-GhuA1X shop" data-id="I615:5032;225:3794" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/shop.png" alt="shop"><img class="dokter-GhuA1X" data-id="I615:5032;225:3795" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/dokter.png" alt="dokter"><img class="penitipan-GhuA1X penitipan" data-id="I615:5032;225:3796" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/penitipan.png" alt="penitipan"><img class="grooming-GhuA1X" data-id="I615:5032;225:3797" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/grooming.png" alt="grooming"><div class="shop-YGlnc9 valign-text-middle shop poppins-semi-bold-black-23px" data-id="I615:5032;225:3798">Shop</div><div class="perawatan-GhuA1X valign-text-middle poppins-semi-bold-black-23px" data-id="I615:5032;225:3799">Perawatan</div><div class="penitipan-YGlnc9 valign-text-middle penitipan poppins-semi-bold-black-23px" data-id="I615:5032;225:3800">Penitipan</div><div class="konsultasi-GhuA1X valign-text-middle poppins-semi-bold-black-23px" data-id="I615:5032;225:3801">Konsultasi</div></div><div class="group-355-ZrInLc" data-id="615:5033"><div class="group-251-05Lfeu" data-id="615:5034"><p class="jl-parangtritis-km-6-sC2ElK poppins-medium-black-16px" data-id="615:5035">Jl. Parangtritis KM 6 Jetis, Panggungharjo, Sewon, Bantul<br><br>Jl. Samas KM 2 Kanutan, Sumbermulyo, Bambanglipuro, Bantul<br><br>Jl. Raya Berbah Pelem Lor, Baturetno, Banguntapan, Bantul</p><div class="frame-236-sC2ElK frame-236" data-id="615:5036"><img class="group-i9UcuF" data-id="615:5037" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/group-1@2x.png" alt="Group"><div class="ling-ling-pet-shop-i9UcuF ling-ling-pet-shop" data-id="615:5043">Ling-Ling Pet Shop</div></div></div><p class="copyright-ling-ling-pet-shop-2025-05Lfeu" data-id="615:5044"> © Copyright Ling-Ling Pet Shop&nbsp;&nbsp;2025.</p><div class="frame-252-05Lfeu" data-id="615:5045"><a href="https://www.facebook.com/lingling.petshop.9/" target="_blank"><div class="group-hSekho" data-id="615:5046"><img class="vector-NrLrX1 vector" data-id="615:5047" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/vector-7.svg" alt="Vector"><img class="vector-BCChzB vector" data-id="615:5048" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/vector-8.svg" alt="Vector"></div></a><a href="https://www.instagram.com/lingling_petshop" target="_blank"><div class="group-TiYhzI" data-id="615:5049"><img class="vector-xdlVZt vector" data-id="615:5050" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/vector-9.svg" alt="Vector"><img class="group-xdlVZt" data-id="615:5051" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/group@2x.png" alt="Group"></div></a><a href="https://foursquare.com/v/lingling-pet-shop/5c03e30b1ffed7002cd9507a" target="_blank"><div class="group-SDsChZ" data-id="615:5055"><img class="vector-FmXUC4 vector" data-id="615:5056" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/vector-10.svg" alt="Vector"><img class="foursquare-FmXUC4" data-id="615:5057" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/foursquare@2x.png" alt="Foursquare"></div></a></div><p class="senin-sabtu-jam-0900-05Lfeu poppins-medium-black-16px" data-id="615:5058">Senin - Sabtu<br>Jam 09.00 - 21.00<br>+62 838-6705-6070</p><div class="jam-buka-05Lfeu poppins-semi-bold-black-16px" data-id="615:5059">Jam Buka</div><img class="mask-group-05Lfeu" data-id="615:5060" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/mask-group@2x.png" alt="Mask group"></div><div class="group-234-ZrInLc" data-id="615:5063"><div class="rectangle-588-rhKMtd" data-id="615:5064"></div><div class="group-235-rhKMtd" data-id="615:5065"><div class="shop-OItU8H valign-text-middle shop poppins-medium-black-16px" data-id="615:5066">Shop</div><div class="home-OItU8H valign-text-middle poppins-semi-bold-orange-16px" data-id="615:5067">Home</div><img class="vector-3-OItU8H" data-id="615:5068" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/vector-3-1.svg" alt="Vector 3"><div class="about-us-OItU8H valign-text-middle poppins-medium-black-16px" data-id="615:5069">About Us</div><div class="dropdown-services-OItU8H" data-id="615:5070"><div class="form-KcywN4" data-id="I615:5070;225:2841"><div class="text-wrap-126wtO" data-id="I615:5070;225:2842"><div class="layanan-grBrVe poppins-medium-black-16px" data-id="I615:5070;225:2843">Layanan</div></div><img class="keyboard-arrow-down-126wtO" data-id="I615:5070;225:2844" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/keyboard-arrow-down-1.svg" alt="Keyboard arrow down"></div></div></div><div class="frame-236-rhKMtd frame-236" data-id="615:5071"><img class="group-yjIvwP" data-id="615:5072" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" anima-src="https://cdn.animaapp.com/projects/67fa6c0b0f1f023f61dbd335/releases/67fa6ca90a551ff812c6d848/img/group-2@2x.png" alt="Group"><div class="ling-ling-pet-shop-yjIvwP ling-ling-pet-shop" data-id="615:5078">Ling-Ling Pet Shop</div></div><div class="hover-signup-rhKMtd" data-id="615:5079"><div class="rectangle-589-hL6jmI rectangle-589" data-id="I615:5079;389:2808"></div><div class="sign-up-hL6jmI valign-text-middle poppins-semi-bold-orange-16px" data-id="I615:5079;389:2809">Sign up</div></div><div class="hover-login-rhKMtd" data-id="615:5080"><div class="rectangle-589-Qrq86j rectangle-589" data-id="I615:5080;389:2811"></div><div class="log-in-Qrq86j valign-text-middle" data-id="I615:5080;389:2812">Log in</div></div></div></div></div><script src="launchpad-js/launchpad-banner.js" async></script><script defer src="https://animaapp.s3.amazonaws.com/static/restart-btn.min.js"></script></body></html>