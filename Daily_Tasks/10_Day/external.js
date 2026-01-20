// let num=[1,2,3,4];
// let num2=[5,6,7,8];

// let update=[...num, ...num2,9]
// console.log(update);


// let user={name:"Anu", age:21};
// let merge={...user, city:"Surat"};

// console.log(merge);

// function sum(...num){
//     return num.reduce((a,b)=> a+b, 0);
// }
// console.log(sum(10,20,30));

// function show(name, age,...skills){
//     console.log(name);
//     console.log(age);
//     console.log(skills);
// }
// show("Anu","21","js","html");

// let num=[1,2,3,4,5,6];

// let part=num.slice(1,3);
// console.log(num);
// console.log(part);


// let num=[1,2,3,4,5,6];
// for(x of num.entries()){
//     console.log(x);
// }

let num=[1,2,3,4,5,6];
num.copyWithin(0,1);
    console.log(num);