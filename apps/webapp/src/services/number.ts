/**
 * Returns a range of numbers between `from` to `to` with a certain `step` in between
 *
 * @param from
 * @param to
 * @param step
 */
export const range = (from: number, to: number, step = 1) =>
  [...Array(Math.floor((to - from) / step) + 1)].map((_, i) => from + i * step)

/**
 * Returns a random integer between intervals
 *
 * @param min
 * @param max
 */
export const randomInteger = (min: number, max: number) =>
  Math.floor(Math.random() * (max - min + 1) + min)
