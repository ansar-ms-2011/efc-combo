const normalMap = {
  a: "ا",
  b: "ب",
  c: "چ",
  d: "د",
  e: "ع",
  f: "ف",
  g: "گ",
  h: "ھ",
  i: "ی",
  j: "ج",
  k: "ک",
  l: "ل",
  m: "م",
  n: "ن",
  o: "ہ",
  p: "پ",
  q: "ق",
  r: "ر",
  s: "س",
  t: "ت",
  u: "ئ",
  v: "ط",
  w: "و",
  x: "ش",
  y: "ے",
  z: "ز",

  ",": "،",
  ".": "۔",
  ";": "؛",
  "[": "ٌ",
  "]": "ْ",
  "`": "ٍ",
  "=": "ٓ",
};

const shiftMap = {
  a: "آ",
  b: "﷽",
  c: "ث",
  d: "ڈ",
  e: "ؑ",
  f: "ٖ",
  g: "غ",
  h: "ح",
  i: "ٰ",
  j: "ض",
  k: "خ",
  l: "ؒ",
  m: "ؐ",
  n: "ں",
  o: "ۃ",
  p: "ُ",
  q: "ﷺ",
  r: "ڑ",
  s: "ص",
  t: "ٹ",
  u: "ء",
  v: "ظ",
  w: "ؤ",
  x: "ژ",
  y: "ۓ",
  z: "ذ",

  "/": "؟",
};

const altMap = {
  "1": "۱",
  "2": "۲",
  "3": "۳",
  "4": "۴",
  "5": "۵",
  "6": "۶",
  "7": "۷",
  "8": "۸",
  "9": "۹",
  "0": "۰",

  a: "أ",
  i: "ي",
  o: "ه",
};

const altShiftMap = {
  a: "إ",
  i: "ى",
  o: "ة",
  q: "ڪ",
};


export default {
  mounted(el, binding) {
    if (!binding.value) return;
    el.setAttribute("dir", "rtl");
    el.style.textAlign = "right";

    el.addEventListener("keydown", (e) => {
      if (e.ctrlKey || e.metaKey) return;

      const key = e.key.toLowerCase();

      let value = null;

      if (e.altKey && e.shiftKey) {
        value = altShiftMap[key];
      } 
      else if (e.altKey) {
        value = altMap[key];
      } 
      else if (e.shiftKey) {
        value = shiftMap[key];
      } 
      else {
        value = normalMap[key];
      }

      if (!value) return;

      e.preventDefault();

      const start = el.selectionStart;
      const end = el.selectionEnd;

      el.value =
        el.value.substring(0, start) +
        value +
        el.value.substring(end);

      const cursor = start + value.length;

      el.setSelectionRange(cursor, cursor);

      el.dispatchEvent(new Event("input"));
    });
  },
};

