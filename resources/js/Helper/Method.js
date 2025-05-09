export function getImgPath(img_path) {
    return img_path && img_path.includes("storage")
        ? `https://akioka.cloud/${img_path}`
        : img_path;
}