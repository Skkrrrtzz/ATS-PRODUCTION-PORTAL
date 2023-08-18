/*!
 * chartjs-plugin-annotation v1.0.2
 * https://www.chartjs.org/chartjs-plugin-annotation/index
 * (c) 2021 chartjs-plugin-annotation Contributors
 * Released under the MIT License
 */
!(function (e, t) {
  "object" == typeof exports && "undefined" != typeof module
    ? (module.exports = t(require("chart.js"), require("chart.js/helpers")))
    : "function" == typeof define && define.amd
    ? define(["chart.js", "chart.js/helpers"], t)
    : ((e = "undefined" != typeof globalThis ? globalThis : e || self)[
        "chartjs-plugin-annotation"
      ] = t(e.Chart, e.Chart.helpers));
})(this, function (e, t) {
  "use strict";
  const n = ["click", "dblclick"],
    o = ["enter", "leave"],
    i = n.concat(o);
  function a(e, t, n, o) {
    if (t.listened)
      switch (n.type) {
        case "mousemove":
        case "mouseout":
          !(function (e, t, n) {
            if (!t.moveListened) return;
            let o;
            "mousemove" === n.type && (o = r(t.elements, n));
            const i = t.hovered;
            (t.hovered = o),
              (function (e, t, n, o) {
                n &&
                  n !== o &&
                  s(e, t, n.options.leave || t.listeners.leave, n);
                o &&
                  o !== n &&
                  s(e, t, o.options.enter || t.listeners.enter, o);
              })(e, t, i, o);
          })(e, t, n);
          break;
        case "click":
          !(function (e, t, n, o) {
            const i = t.listeners,
              a = r(t.elements, n);
            if (a) {
              const n = a.options,
                r = n.dblclick || i.dblclick,
                l = n.click || i.click;
              a.clickTimeout
                ? (clearTimeout(a.clickTimeout),
                  delete a.clickTimeout,
                  s(e, t, r, a))
                : r
                ? (a.clickTimeout = setTimeout(() => {
                    delete a.clickTimeout, s(e, t, l, a);
                  }, o.dblClickSpeed))
                : s(e, t, l, a);
            }
          })(e, t, n, o);
      }
  }
  function s(e, n, o, i) {
    t.callback(o, [{ chart: e, element: i }]);
  }
  function r(e, n) {
    let o = Number.POSITIVE_INFINITY;
    return e
      .filter((e) => e.options.display && e.inRange(n.x, n.y))
      .reduce((e, i) => {
        const a = i.getCenterPoint(),
          s = t.distanceBetweenPoints(n, a);
        return s < o ? ((e = [i]), (o = s)) : s === o && e.push(i), e;
      }, [])
      .sort((e, t) => e._index - t._index)
      .slice(0, 1)[0];
  }
  const l = Math.PI,
    d = l / 2;
  function c(e, n, o) {
    return (
      (n = "number" == typeof n ? n : e.parse(n)),
      t.isFinite(n) ? e.getPixelForValue(n) : o
    );
  }
  function h(e, t, n, o, i, a) {
    if ((e.beginPath(), a)) {
      const s = Math.min(a, i / 2, o / 2),
        r = t + s,
        c = n + s,
        h = t + o - s,
        f = n + i - s;
      e.moveTo(t, c),
        r < h && c < f
          ? (e.arc(r, c, s, -l, -d),
            e.arc(h, c, s, -d, 0),
            e.arc(h, f, s, 0, d),
            e.arc(r, f, s, d, l))
          : r < h
          ? (e.moveTo(r, n), e.arc(h, c, s, -d, d), e.arc(r, c, s, d, l + d))
          : c < f
          ? (e.arc(r, c, s, -l, 0), e.arc(r, f, s, 0, l))
          : e.arc(r, c, s, -l, l),
        e.closePath(),
        e.moveTo(t, n);
    } else e.rect(t, n, o, i);
  }
  class f extends e.Element {
    inRange(e, t, n) {
      const {
        x: o,
        y: i,
        width: a,
        height: s,
      } = this.getProps(["x", "y", "width", "height"], n);
      return e >= o && e <= o + a && t >= i && t <= i + s;
    }
    getCenterPoint(e) {
      const {
        x: t,
        y: n,
        width: o,
        height: i,
      } = this.getProps(["x", "y", "width", "height"], e);
      return { x: t + o / 2, y: n + i / 2 };
    }
    draw(e) {
      const { x: t, y: n, width: o, height: i, options: a } = this;
      e.save(),
        (e.lineWidth = a.borderWidth),
        (e.strokeStyle = a.borderColor),
        (e.fillStyle = a.backgroundColor),
        e.setLineDash(a.borderDash),
        (e.lineDashOffset = a.borderDashOffset),
        h(e, t, n, o, i, a.cornerRadius),
        e.fill(),
        a.borderWidth && e.stroke(),
        e.restore();
    }
    resolveElementProperties(e, t) {
      const n = e.scales[t.xScaleID],
        o = e.scales[t.yScaleID];
      let i,
        a,
        { top: s, left: r, bottom: l, right: d } = e.chartArea;
      return n || o
        ? (n &&
            ((i = c(n, t.xMin, r)),
            (a = c(n, t.xMax, d)),
            (r = Math.min(i, a)),
            (d = Math.max(i, a))),
          o &&
            ((i = c(o, t.yMin, l)),
            (a = c(o, t.yMax, s)),
            (s = Math.min(i, a)),
            (l = Math.max(i, a))),
          { x: r, y: s, x2: d, y2: l, width: d - r, height: l - s })
        : { options: {} };
    }
  }
  (f.id = "boxAnnotation"),
    (f.defaults = {
      display: !0,
      adjustScaleRange: !0,
      borderDash: [],
      borderDashOffset: 0,
      borderWidth: 1,
      cornerRadius: 0,
      xScaleID: "x",
      xMin: void 0,
      xMax: void 0,
      yScaleID: "y",
      yMin: void 0,
      yMax: void 0,
    }),
    (f.defaultRoutes = { borderColor: "color", backgroundColor: "color" });
  const u = Math.PI,
    x = (e, t, n) => ({ x: e.x + n * (t.x - e.x), y: e.y + n * (t.y - e.y) }),
    y = (e, t, n) => x(t, n, Math.abs((e - t.y) / (n.y - t.y))).x,
    b = (e, t, n) => x(t, n, Math.abs((e - t.x) / (n.x - t.x))).y;
  function g({ x: e, y: t }, n, { top: o, right: i, bottom: a, left: s }) {
    return (
      e < s && ((t = n.x < s ? NaN : b(s, { x: e, y: t }, n)), (e = s)),
      e > i && ((t = n.x > i ? NaN : b(i, { x: e, y: t }, n)), (e = i)),
      t < o && ((e = n.y < o ? NaN : y(o, { x: e, y: t }, n)), (t = o)),
      t > a && ((e = n.y > a ? NaN : y(a, { x: e, y: t }, n)), (t = a)),
      { x: e, y: t }
    );
  }
  class p extends e.Element {
    intersects(e, t, n = 0.001) {
      const o = (e) => e * e,
        { x: i, y: a, x2: s, y2: r } = this,
        l = s - i,
        d = r - a,
        c = o(l) + o(d),
        h = 0 === c ? -1 : ((e - i) * l + (t - a) * d) / c;
      let f, u;
      return (
        h < 0
          ? ((f = i), (u = a))
          : h > 1
          ? ((f = s), (u = r))
          : ((f = i + h * l), (u = a + h * d)),
        o(e - f) + o(t - u) < n
      );
    }
    labelIsVisible() {
      const e = this.options.label;
      return e && e.enabled && e.content;
    }
    isOnLabel(e, t) {
      const { labelRect: n } = this;
      if (!n || !this.labelIsVisible()) return !1;
      const { x: o, y: i } =
        ((a = { x: e, y: t }),
        (s = n),
        (r = -n.rotation),
        (l = Math.cos(r)),
        (d = Math.sin(r)),
        (c = s.x),
        (h = s.y),
        {
          x: c + l * (a.x - c) - d * (a.y - h),
          y: h + d * (a.x - c) + l * (a.y - h),
        });
      var a, s, r, l, d, c, h;
      const f = n.width / 2,
        u = n.height / 2;
      return o >= n.x - f && o <= n.x + f && i >= n.y - u && i <= n.y + u;
    }
    inRange(e, t) {
      const n = this.options.borderWidth || 1;
      return this.intersects(e, t, n) || this.isOnLabel(e, t);
    }
    getCenterPoint() {
      return { x: (this.x2 + this.x) / 2, y: (this.y2 + this.y) / 2 };
    }
    draw(e) {
      const { x: t, y: n, x2: o, y2: i, options: a } = this;
      e.save(),
        (e.lineWidth = a.borderWidth),
        (e.strokeStyle = a.borderColor),
        e.setLineDash(a.borderDash),
        (e.lineDashOffset = a.borderDashOffset),
        e.beginPath(),
        e.moveTo(t, n),
        e.lineTo(o, i),
        e.stroke(),
        e.restore();
    }
    drawLabel(e, n) {
      this.labelIsVisible() &&
        (e.save(),
        (function (e, n, o) {
          const i = n.options.label;
          e.font = t.toFontString(i.font);
          const { width: a, height: s } = (function (e, n) {
              const o = n.content;
              if (o instanceof Image)
                return {
                  width: m(o.width, n.width) + 2 * n.xPadding,
                  height: m(o.height, n.height) + 2 * n.yPadding,
                };
              const i = t.isArray(o) ? o : [o],
                a = i.length;
              let s = 0;
              for (let t = 0; t < a; t++) {
                const n = i[t];
                M.has(n) || M.set(n, e.measureText(n).width),
                  (s = Math.max(s, M.get(n)));
              }
              return (
                (s += 2 * n.xPadding),
                { width: s, height: a * n.font.size + (a + 1) * n.yPadding }
              );
            })(e, i),
            r = (n.labelRect = (function (e, n, o, i) {
              const a = e.options.label,
                {
                  xAdjust: s,
                  yAdjust: r,
                  xPadding: l,
                  yPadding: d,
                  position: c,
                } = a,
                h = { x: e.x, y: e.y },
                f = { x: e.x2, y: e.y2 },
                y =
                  "auto" === a.rotation
                    ? (function (e) {
                        const { x: t, y: n, x2: o, y2: i } = e,
                          a = Math.atan2(i - n, o - t);
                        return a > u / 2 ? a - u : a < u / -2 ? a + u : a;
                      })(e)
                    : t.toRadians(a.rotation),
                b = (function (e, t, n) {
                  const o = Math.cos(n),
                    i = Math.sin(n);
                  return {
                    w: Math.abs(e * o) + Math.abs(t * i),
                    h: Math.abs(e * i) + Math.abs(t * o),
                  };
                })(n, o, y),
                g = (function (e, t, n, o) {
                  let i = 0.5;
                  const a = (function (e, t) {
                      const { x: n, x2: o, y: i, y2: a } = e,
                        s = Math.min(i, a) - t.top,
                        r = Math.min(n, o) - t.left,
                        l = t.bottom - Math.max(i, a),
                        d = t.right - Math.max(n, o);
                      return {
                        x: Math.min(r, d),
                        y: Math.min(s, l),
                        dx: r < d ? 1 : -1,
                        dy: s < l ? 1 : -1,
                      };
                    })(e, o),
                    s = e.options.label;
                  "start" === t
                    ? (i = w({ w: e.x2 - e.x, h: e.y2 - e.y }, n, s, a))
                    : "end" === t &&
                      (i = 1 - w({ w: e.x - e.x2, h: e.y - e.y2 }, n, s, a));
                  return i;
                })(e, c, b, i),
                p = x(h, f, g),
                m = { size: b.w, min: i.left, max: i.right, padding: l },
                M = { size: b.h, min: i.top, max: i.bottom, padding: d };
              return {
                x: v(p.x, m) + s,
                y: v(p.y, M) + r,
                width: n,
                height: o,
                rotation: y,
              };
            })(n, a, s, o));
          if (
            (e.translate(r.x, r.y),
            e.rotate(r.rotation),
            (e.fillStyle = i.backgroundColor),
            h(e, -a / 2, -s / 2, a, s, i.cornerRadius),
            e.fill(),
            (e.fillStyle = i.color),
            t.isArray(i.content))
          ) {
            e.textAlign = i.textAlign;
            const t = (function (e, t) {
              const { textAlign: n, xPadding: o } = e;
              if ("start" === n) return -t / 2 + o;
              if ("end" === n) return +t / 2 - o;
              return 0;
            })(i, a);
            let n = -s / 2 + i.yPadding;
            for (let o = 0; o < i.content.length; o++)
              (e.textBaseline = "top"),
                e.fillText(i.content[o], t, n),
                (n += i.font.size + i.yPadding);
          } else if (i.content instanceof Image) {
            const t = -a / 2 + i.xPadding,
              n = -s / 2 + i.yPadding;
            e.drawImage(
              i.content,
              t,
              n,
              a - 2 * i.xPadding,
              s - 2 * i.yPadding
            );
          } else
            (e.textAlign = "center"),
              (e.textBaseline = "middle"),
              e.fillText(i.content, 0, 0);
        })(e, this, n),
        e.restore());
    }
    resolveElementProperties(e, t) {
      const n = e.scales[t.scaleID];
      let o,
        i,
        { top: a, left: s, bottom: r, right: l } = e.chartArea;
      if (n)
        (o = c(n, t.value, NaN)),
          (i = c(n, t.endValue, o)),
          n.isHorizontal() ? ((s = o), (l = i)) : ((a = o), (r = i));
      else {
        const n = e.scales[t.xScaleID],
          o = e.scales[t.yScaleID];
        n && ((s = c(n, t.xMin, s)), (l = c(n, t.xMax, l))),
          o && ((a = c(o, t.yMin, a)), (r = c(o, t.yMax, r)));
      }
      return (function (e, t, n) {
        const { x: o, y: i } = g(e, t, n),
          { x: a, y: s } = g(t, e, n);
        return {
          x: o,
          y: i,
          x2: a,
          y2: s,
          width: Math.abs(a - o),
          height: Math.abs(s - i),
        };
      })({ x: s, y: a }, { x: l, y: r }, e.chartArea);
    }
  }
  function m(e, t) {
    return "number" == typeof t
      ? t
      : "string" == typeof t
      ? ("string" == typeof (n = t) && n.endsWith("%") && parseFloat(n) / 100) *
        e
      : e;
    var n;
  }
  (p.id = "lineAnnotation"),
    (p.defaults = {
      display: !0,
      adjustScaleRange: !0,
      borderWidth: 2,
      borderDash: [],
      borderDashOffset: 0,
      label: {
        backgroundColor: "rgba(0,0,0,0.8)",
        drawTime: void 0,
        font: {
          family: void 0,
          lineHeight: void 0,
          size: void 0,
          style: "bold",
          weight: void 0,
        },
        color: "#fff",
        xPadding: 6,
        yPadding: 6,
        rotation: 0,
        cornerRadius: 6,
        position: "center",
        xAdjust: 0,
        yAdjust: 0,
        textAlign: "center",
        enabled: !1,
        content: null,
      },
      value: void 0,
      endValue: void 0,
      scaleID: void 0,
      xScaleID: "x",
      xMin: void 0,
      xMax: void 0,
      yScaleID: "y",
      yMin: void 0,
      yMax: void 0,
    }),
    (p.defaultRoutes = { borderColor: "color" });
  const M = new Map();
  function w(e, t, n, o) {
    const { xPadding: i, yPadding: a } = n,
      s = e.w * o.dx,
      r = e.h * o.dy,
      l = s > 0 && (t.w / 2 + i - o.x) / s,
      d = r > 0 && (t.h / 2 + a - o.y) / r;
    return ((e, t, n) => Math.min(n, Math.max(t, e)))(Math.max(l, d), 0, 0.25);
  }
  function v(e, t) {
    const { size: n, min: o, max: i, padding: a } = t,
      s = n / 2;
    return n > i - o
      ? (i + o) / 2
      : (o >= e - a - s && (e = o + a + s),
        i <= e + a + s && (e = i - a - s),
        e);
  }
  class D extends f {
    inRange(e, t) {
      return (function (e, t) {
        const { width: n, height: o } = t,
          i = t.getCenterPoint(!0),
          a = n / 2,
          s = o / 2;
        if (a <= 0 || s <= 0) return !1;
        return (
          Math.pow(e.x - i.x, 2) / Math.pow(a, 2) +
            Math.pow(e.y - i.y, 2) / Math.pow(s, 2) <=
          1
        );
      })({ x: e, y: t }, this);
    }
    draw(e) {
      const { width: t, height: n, options: o } = this,
        i = this.getCenterPoint();
      e.save(),
        e.beginPath(),
        (e.lineWidth = o.borderWidth),
        (e.strokeStyle = o.borderColor),
        (e.fillStyle = o.backgroundColor),
        e.setLineDash(o.borderDash),
        (e.lineDashOffset = o.borderDashOffset),
        e.ellipse(i.x, i.y, n / 2, t / 2, Math.PI / 2, 0, 2 * Math.PI),
        e.fill(),
        e.stroke(),
        e.restore();
    }
  }
  (D.id = "ellipseAnnotation"),
    (D.defaults = {
      display: !0,
      adjustScaleRange: !0,
      borderDash: [],
      borderDashOffset: 0,
      borderWidth: 1,
      xScaleID: "x",
      xMin: void 0,
      xMax: void 0,
      yScaleID: "y",
      yMin: void 0,
      yMax: void 0,
    }),
    (D.defaultRoutes = { borderColor: "color", backgroundColor: "color" });
  class P extends e.Element {
    inRange(e, t) {
      const { width: n, options: o } = this,
        i = this.getCenterPoint(!0),
        a = n / 2 + o.borderWidth;
      return (
        !(a <= 0) &&
        Math.pow(e - i.x, 2) + Math.pow(t - i.y, 2) <= Math.pow(a, 2)
      );
    }
    getCenterPoint(e) {
      const { x: t, y: n } = this.getProps(["x", "y"], e);
      return { x: t, y: n };
    }
    draw(e) {
      const { x: t, y: n, width: o, options: i } = this;
      e.save(),
        (e.lineWidth = i.borderWidth),
        (e.strokeStyle = i.borderColor),
        (e.fillStyle = i.backgroundColor),
        e.setLineDash(i.borderDash),
        (e.lineDashOffset = i.borderDashOffset),
        e.beginPath(),
        e.arc(t, n, o / 2, 0, 2 * Math.PI),
        e.fill(),
        e.stroke(),
        e.restore();
    }
    resolveElementProperties(e, t) {
      const { chartArea: n, scales: o } = e,
        i = o[t.xScaleID],
        a = o[t.yScaleID];
      let s = n.width / 2,
        r = n.height / 2;
      return (
        i && (s = c(i, t.xValue, s)),
        a && (r = c(a, t.yValue, r)),
        { x: s, y: r, width: 2 * t.radius, height: 2 * t.radius }
      );
    }
  }
  (P.id = "pointAnnotation"),
    (P.defaults = {
      display: !0,
      adjustScaleRange: !0,
      borderDash: [],
      borderDashOffset: 0,
      borderWidth: 1,
      radius: 10,
      xScaleID: "x",
      xValue: void 0,
      yScaleID: "y",
      yValue: void 0,
    }),
    (P.defaultRoutes = { borderColor: "color", backgroundColor: "color" });
  const I = new Map(),
    k = { box: f, line: p, ellipse: D, point: P };
  Object.keys(k).forEach((t) => {
    e.defaults.describe(`elements.${k[t].id}`, {
      _fallback: "plugins.annotation",
    });
  });
  var S = {
    id: "annotation",
    version: "1.0.2",
    afterRegister() {
      e.Chart.register(k);
    },
    afterUnregister() {
      e.Chart.unregister(k);
    },
    beforeInit(e) {
      I.set(e, {
        annotations: [],
        elements: [],
        listeners: {},
        listened: !1,
        moveListened: !1,
      });
    },
    beforeUpdate(e, n, o) {
      const i = (I.get(e).annotations = []);
      let a = o.annotations;
      t.isObject(a)
        ? Object.keys(a).forEach((e) => {
            const n = a[e];
            t.isObject(n) && ((n.id = e), i.push(n));
          })
        : t.isArray(a) && i.push(...a);
    },
    afterDataLimits(e, n) {
      const o = I.get(e);
      !(function (e, n, o) {
        const i = (function (e, n) {
          const o = e.axis,
            i = e.id,
            a = o + "ScaleID";
          let s = t.valueOrDefault(e.min, Number.NEGATIVE_INFINITY),
            r = t.valueOrDefault(e.max, Number.POSITIVE_INFINITY);
          for (const t of n)
            if (t.scaleID === i)
              for (const n of ["value", "endValue"]) {
                const o = t[n];
                if (o) {
                  const t = e.parse(o);
                  (s = Math.min(s, t)), (r = Math.max(r, t));
                }
              }
            else if (t[a] === i)
              for (const n of [o + "Min", o + "Max", o + "Value"]) {
                const o = t[n];
                if (o) {
                  const t = e.parse(o);
                  (s = Math.min(s, t)), (r = Math.max(r, t));
                }
              }
          return { min: s, max: r };
        })(n, o);
        let a = !1;
        t.isFinite(i.min) &&
          void 0 === n.options.min &&
          void 0 === n.options.suggestedMin &&
          ((a = n.min !== i.min), (n.min = i.min));
        t.isFinite(i.max) &&
          void 0 === n.options.max &&
          void 0 === n.options.suggestedMax &&
          ((a = n.max !== i.max), (n.max = i.max));
        a &&
          "function" == typeof n.handleTickRangeOptions &&
          n.handleTickRangeOptions();
      })(
        0,
        n.scale,
        o.annotations.filter((e) => e.display && e.adjustScaleRange)
      );
    },
    afterUpdate(t, a, s) {
      const r = I.get(t);
      !(function (e, t, a) {
        const s = t.annotations || [];
        (t.listened = !1),
          (t.moveListened = !1),
          i.forEach((e) => {
            "function" == typeof a[e] &&
              ((t.listened = !0), (t.listeners[e] = a[e]));
          }),
          o.forEach((e) => {
            "function" == typeof a[e] && (t.moveListened = !0);
          }),
          (t.listened && t.moveListened) ||
            s.forEach((e) => {
              t.listened ||
                n.forEach((n) => {
                  "function" == typeof e[n] && (t.listened = !0);
                }),
                t.moveListened ||
                  o.forEach((n) => {
                    "function" == typeof e[n] &&
                      ((t.listened = !0), (t.moveListened = !0));
                  });
            });
      })(0, r, s),
        (function (t, n, o, i) {
          const a = (function (t, n, o) {
              if ("reset" === o || "none" === o || "resize" === o) return T;
              return new e.Animations(t, n);
            })(t, o.animations, i),
            s = n.annotations,
            r = (function (e, t) {
              const n = t.length,
                o = e.length;
              if (o < n) {
                const t = n - o;
                e.splice(o, 0, ...new Array(t));
              } else o > n && e.splice(n, o - n);
              return e;
            })(n.elements, s);
          for (let e = 0; e < s.length; e++) {
            const n = s[e];
            let o = r[e];
            const i = k[n.type] || k.line;
            (o && o instanceof i) || (o = r[e] = new i());
            const l = C(n.setContext(A(t, o, n))),
              d = o.resolveElementProperties(t, l);
            (d.skip = isNaN(d.x) || isNaN(d.y)),
              (d.options = l),
              a.update(o, d);
          }
        })(t, r, s, a.mode);
    },
    beforeDatasetsDraw(e) {
      j(e, "beforeDatasetsDraw");
    },
    afterDatasetsDraw(e) {
      j(e, "afterDatasetsDraw");
    },
    beforeDraw(e) {
      j(e, "beforeDraw");
    },
    afterDraw(e) {
      j(e, "afterDraw");
    },
    beforeEvent(e, t, n) {
      a(e, I.get(e), t.event, n);
    },
    destroy(e) {
      I.delete(e);
    },
    _getState: (e) => I.get(e),
    defaults: {
      drawTime: "afterDatasetsDraw",
      dblClickSpeed: 350,
      animations: {
        numbers: {
          properties: ["x", "y", "x2", "y2", "width", "height"],
          type: "number",
        },
      },
      label: { drawTime: null },
    },
    descriptors: {
      _indexable: !1,
      _scriptable: (e) => !i.includes(e),
      annotations: {
        _allKeys: !1,
        _fallback: (e, t) => `elements.${k[t.type || "line"].id}`,
      },
    },
    additionalOptionScopes: [""],
  };
  const T = { update: Object.assign };
  function C(e) {
    const t = k[e.type] || k.line,
      n = {};
    (n.id = e.id),
      (n.type = e.type),
      (n.drawTime = e.drawTime),
      Object.assign(n, O(e, t.defaults), O(e, t.defaultRoutes));
    for (const t of i) n[t] = e[t];
    return n;
  }
  function O(e, n) {
    const o = {};
    for (const i of Object.keys(n)) {
      const a = n[i],
        s = e[i];
      o[i] = t.isObject(a) ? O(s, a) : s;
    }
    return o;
  }
  function A(e, t, n) {
    return (
      t.$context ||
      (t.$context = Object.assign(Object.create(e.getContext()), {
        element: t,
        id: n.id,
        type: "annotation",
      }))
    );
  }
  function j(e, n) {
    const { ctx: o, chartArea: i } = e,
      a = I.get(e).elements.filter((e) => !e.skip && e.options.display);
    t.clipArea(o, i),
      a.forEach((e) => {
        e.options.drawTime === n && e.draw(o);
      }),
      t.unclipArea(o),
      a.forEach((e) => {
        "drawLabel" in e &&
          e.options.label &&
          (e.options.label.drawTime || e.options.drawTime) === n &&
          e.drawLabel(o, i);
      });
  }
  return e.Chart.register(S), S;
});
