// let tasks = [];       // Store all tasks
// let editIndex = null; // Track editing task index

// // CREATE & UPDATE
// function addTask() {
//   const taskInput = document.getElementById("taskInput");
//   const taskValue = taskInput.value.trim();

//   if (taskValue === "") {
//     alert("Please enter a task");
//     return;
//   }

//   if (editIndex === null) {
//     // CREATE
//     tasks.push({ task: taskValue });
//   } else {
//     // UPDATE
//     tasks[editIndex].task = taskValue;
//     editIndex = null;
//   }

//   taskInput.value = "";
//   displayTasks();
// }

// // READ
// function displayTasks() {
//   const taskList = document.getElementById("taskList");
//   taskList.innerHTML = "";

//   tasks.forEach((item, index) => {
//     taskList.innerHTML += `
//       <li>
//         ${item.task}
//         <div>
//           <button onclick="editTask(${index})">Edit</button>
//           <button onclick="deleteTask(${index})">Delete</button>
//         </div>
//       </li>
//     `;
//   });
// }

// // DELETE
// function deleteTask(index) {
//   tasks.splice(index, 1);
//   displayTasks();
// }

// // EDIT
// function editTask(index) {
//   document.getElementById("taskInput").value = tasks[index].task;
//   editIndex = index;
// }



let tasks=[];
let editIndex=null;

function addTask(){
    const taskInput=document.getElementById("taskInput");
    const taskValue=taskInput.value.trim();

    if(taskValue===""){
        alert("Please enter the task");
        return;
    }
    if(editIndex===null){
        tasks.push({task: taskValue});
    }else{
        tasks[editIndex].task=taskValue;
        editIndex=null;
    }
    taskInput.value="";
    displayTasks();
}
    function displayTasks(){
        const taskList=document.getElementById("taskList");
        taskList.innerHTML="";

        tasks.forEach((item,index) => {
            taskList.innerHTML += `
            <li>
            ${item.task}
                <div>
                    <button onClick="editTask(${index})">Edit</button>
                    <button onClick="deleteTask(${index})">Delete</button>
                </div>
            </li>
            `;
        });
    }

        function editTask(index){
           document.getElementById("taskInput").value=tasks[index].task;
           editIndex=index;
        }

        function deleteTask(index){
            tasks.splice(index,1);
            displayTasks();
        }
