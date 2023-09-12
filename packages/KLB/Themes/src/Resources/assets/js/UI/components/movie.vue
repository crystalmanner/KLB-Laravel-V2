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
            {link:"https://www.youtube.com/embed/pRnZjrGz_Lg"},
            {link:"https://www.youtube.com/embed/dX6_PInAQ4M"},

]
}
}
}















</script>

