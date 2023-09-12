<template>

    <div >

        <iframe  v-for = "item in section1Tabs" width="560" height="315" :src="item.link" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        <br>
    </div>

</template>

<script>









export default {

    data() {
        return {
            section1Tabs: [
                {link:"https://cdn.shopify.com/s/files/1/0256/6140/7277/products/extra-firming-treatment-essence-bouncinessall-skin-typesanti-ageing-958016.jpg?v=1607027439"},
                {link:"https://www.youtube.com/embed/xpyrefzvTpI"},
                {link: "https://www.youtube.com/embed/KQrnI02qz1w"}
            ]
        }
    }
}














</script>


