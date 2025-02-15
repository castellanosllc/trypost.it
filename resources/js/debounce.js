export default function debounce(callback, wait = 1000) {
    let timeoutId = null;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            callback(...args);
        }, wait);
    };
}
