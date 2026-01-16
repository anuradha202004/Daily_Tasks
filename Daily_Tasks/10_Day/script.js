// // DATA STORAGE - ARRAY OF OBJECTS
// let products = [];
// let editIndex = null;

// // ELEMENT SELECTION
// const nameInput = document.getElementById("name");
// const priceInput = document.getElementById("price");
// const addBtn = document.getElementById("addBtn");
// const updateBtn = document.getElementById("updateBtn");
// const productList = document.getElementById("productList");

// // ADD PRODUCT
// addBtn.addEventListener("click", function () {
//     let product = {
//         name: nameInput.value,
//         price: priceInput.value
//     };

//     products.push(product);
//     clearForm();
//     displayProducts();
// });

// // UPDATE PRODUCT
// updateBtn.addEventListener("click", function () {
//     products[editIndex].name = nameInput.value;
//     products[editIndex].price = priceInput.value;

//     clearForm();
//     displayProducts();

//     addBtn.style.display = "inline";
//     updateBtn.style.display = "none";
// });

// // DISPLAY PRODUCTS
// function displayProducts() {
//     productList.innerHTML = "";

//     products.forEach(function (item, index) {
//         let div = document.createElement("div");
//         div.className = "product";

//         div.innerHTML = `
//             ${index + 1}. ${item.name} - ₹${item.price}
//             <button onclick="editProduct(${index})">Edit</button>
//             <button onclick="deleteProduct(${index})">Delete</button>
//         `;

//         productList.appendChild(div);
//     });
// }

// // DELETE PRODUCT
// function deleteProduct(index) {
//     products.splice(index, 1);
//     displayProducts();
// }

// // EDIT PRODUCT
// function editProduct(index) {
//     nameInput.value = products[index].name;
//     priceInput.value = products[index].price;

//     editIndex = index;
//     addBtn.style.display = "none";
//     updateBtn.style.display = "inline";
// }

// // CLEAR FORM
// function clearForm() {
//     nameInput.value = "";
//     priceInput.value = "";
// }


let products = [];
let editIndex = null;

const nameInput=document.getElementById("name");
const priceInput=document.getElementById("price");
const addBtn=document.getElementById("addBtn");
const updateBtn=document.getElementById("updateBtn")
const productList=document.getElementById("productList");

//Add Product
addBtn.addEventListener("click" , function(){
    let product={ 
        name: nameInput.value,
        price: priceInput.value
    };

    products.push(product);
    clearForm();
    displayProducts();
});

//Update Product
updateBtn.addEventListener("click", function(){
    products[editIndex].name=nameInput.value;
    products[editIndex].price=priceInput.value;

    clearForm();
    displayProducts();

    addBtn.style.display= "inline";
    updateBtn.style.display= "none";
});

//Display product

function displayProducts() {
    productList.innerHTML="";

    products.forEach(function (item, index){
        let div=document.createElement("div");
        div.className="product";

        div.innerHTML= `${index + 1}. ${item.name} = ₹${item.price}
        <button onclick="editProduct(${index})">Edit</button>
        <button onclick="deleteProduct(${index})">Delete</button>
         `;
 
         productList.appendChild(div);
    });
}

//Delete

 function deleteProduct(index){
    products.splice(index ,1);
    displayProducts();
 }

 //Edit
 function editProduct(index){
    nameInput.value=products[index].name;
    priceInput.value=products[index].price;

    editIndex=index;

    addBtn.style.display="none";
    updateBtn.style.display="inline";
}

//clear form
function clearForm(){
    nameInput.value="";
    priceInput.value="";
}