const view = document.querySelector(".view");
const modal = document.querySelector(".modal-pfp");
const openBtn = document.querySelector("#openModalBtn");
const closeBtn = document.querySelector("#closeModalBtn");
const images = document.querySelectorAll(".img-pfp img");
const mainImg = document.querySelector(".mainImg");
const tmpImg = document.querySelector(".tmpImg");
const validMainImg = document.querySelector(".valid-mainImg");
const inputMainImg = document.querySelector(".input-main-img");

function open(){
    view.style.display = "flex";
}

function close() {
    view.style.display = "none";
}

function removeClassSelected(){
    for (let image of images) {
        image.classList.remove("selected");
    }
}

for (let image of images) {
    image.addEventListener("click", function () {
        removeClassSelected();
        tmpImg.src = image.src;
        image.classList.add("selected");
    });
}

function changeMainImg() {
    let filename = tmpImg.src.split("/images")[1];
    filename = "/images" + filename;
    mainImg.src = filename;
    inputMainImg.value = filename;
    close();
}

openBtn.addEventListener("click", open);

closeBtn.addEventListener("click", close);

validMainImg.addEventListener("click", changeMainImg);

view.addEventListener("click", (e)=>{
    if (e.target === view) {
        view.style.display = "none";
    }
})
