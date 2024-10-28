import axios from "axios";

export async function getCategories() {
    try {
        const res = await axios.get(route("movie2.getCategories"));
        console.log(res.data);

        return res.data;
    } catch (error) {
        console.log(error);
        return [];
    }
}

export async function getCategoryTags(category_id) {
    try {
        const res = await axios.get(route("movie2.getTags", { category_id: category_id }));
        console.log(res.data);

        return res.data;
    } catch (error) {
        console.log(error);
        return [];
    }
}
