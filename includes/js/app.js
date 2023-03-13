$(document).ready(function () {
  let socket = null;
  const connectToSocket = () => {
    try{
    let closeSocket = io("http://localhost:5000");
    // let closeSocket = io("https://connectcurb-app.onrender.com/");
    socket = closeSocket;
    return () => {
      closeSocket?.close();
    };
  }catch(e){
    
  }
  };
  connectToSocket();
  let userId = "";
  let userAnswer = {};
  let addAnswer = {};
  let addAnswerOptions = false;
  let submitForm = true;
  let addAnswerOptionsArray = [];
  let userAnswers = [];
  let lastPrompt = {};
  let question_id = 0;
  

  var reply_forms = document.querySelectorAll(".reply_form");
  var add_list_arrays = document.querySelectorAll(".add_list_array");
  var lists_htmls = document.querySelectorAll(".lists_html");
  var submit_prompt_btn = document.querySelectorAll(".submit_prompt_btn");
  var add_prompt_agains = document.querySelectorAll(".add_prompt_again");
  var add_prompt_forms = document.querySelectorAll(".add_prompt_formsone");
  var inputs = document.querySelectorAll(".answer_input");
  var add_prompt_forms_inputs = document.querySelectorAll(
    ".add_prompt_forms_input"
  );
  const genes = document.querySelectorAll(".messages_wrapper");
  const prompts_wrapper = document.querySelectorAll(".prompts_wrapper");
  const maillists = document.querySelectorAll(".maillistmsgs");




  //set lists state and prompt
  add_prompt_agains.forEach((add_prompt_again)=>{
    add_prompt_again.onclick = (e)=>{
      submit_prompt_btn.forEach((btn)=>{
        btn.innerHTML = "Add Prompt";
      });
      submitForm = true;    
      if(submitForm){
        addAnswerOptions = true;  
      }else{
      addAnswerOptions = false;  
      }
    }
  });
  add_list_arrays.forEach((add_list_array)=>{
    add_list_array.onclick = (e)=>{
      submit_prompt_btn.forEach((btn)=>{
        btn.innerHTML = "Add List";
      });
      addAnswerOptions = true;
      submitForm = false;
    }
  });

  socket.on("me", (id) => {
    //l get my own id then start the process
    userId = id;
    socket.emit("get_question", { question_id, user_id: id });

    reply_forms.forEach((reply_form) => {
      reply_form.addEventListener("submit", function (e) {
        e.preventDefault();
        inputs.forEach((input) => {
          if (input.value) {
            question_id++;
            userAnswer = { user_id: id, answer: input.value, question_id };
            socket.emit("answer_question", userAnswer);
            userAnswers.push({
              prompt: lastPrompt,
              answer: input.value,
              question_id,
            });
            maillists.forEach((maillist, index) => {
              let data_html = `
            <a href="javascript:void(0)" class="mailpreview attachment">
            <div class="imgpic"><i class="-o"></i></div>
            <div class="textmail">
              <strong>${input.value}</strong>
              <p>answer</p>
              <span class="btn btn-default attachdownload"
                ></span>
            </div>
          </a>
            `;
              maillist.innerHTML += data_html;
            }); //set value added by the user also
            input.value = "";
          }
        });
      });
    });

    //start add prompt form
    add_prompt_forms.forEach((form) => {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
       
        add_prompt_forms_inputs.forEach((add_prompt_forms_input) => {
          if (add_prompt_forms_input.value) {
            if(!submitForm){
           addAnswerOptionsArray.push({name: add_prompt_forms_input.value});
            }// l need to change this function to make it not send whilst adding options
            if(submitForm){
            if (addAnswerOptions) {
              addAnswer = {
                prompt: add_prompt_forms_input.value,
                isList: true,
                options: addAnswerOptionsArray,
                user_id: id,
              };
            } else {
              addAnswer = {
                prompt: add_prompt_forms_input.value,
                isList: false,
                user_id: id,
              };
            }
            socket.emit("add_question", addAnswer);
            //append the value send
            prompts_wrapper.forEach((prompty) => {
              let data_html = `
                  <tr>
                  <td class="primary statusmail">
                    <input type="checkbox" id="check2" class="checkbox" />
                    <label for="check2"></label>
                    <a href="javascript:void(0)">
                      <div class="textmail">
                        <strong>${addAnswer?.prompt}</strong>
                        <!-- <span class="pull-right text-muted"> 
                          12 Jul 16 | 11:00 am
                        </span> -->
                        <p>
                         does have list : ${addAnswer?.isList}
                        </p>
                        <button style="margin:8px;"  class="btn btn-danger detele-prompt" data-prompt="${addAnswer?.prompt}">delete</button>
                      </div>
                    </a>
                  </td>
                </tr>`;
              tr.innerHTML += data_html;

              $(".prompts_wrapper").append(tr);
              $(".detele-prompt").on("click", () => {
                removePrompt($(".detele-prompt").attr("data-prompt"));
              });
            });
          }else{
            lists_htmls.forEach((lists_html) => {
              let data_html = `
                   <li>${add_prompt_forms_input.value}</li>
                 `;
                 lists_html.innerHTML += data_html;
            });
          }
            add_prompt_forms_input.value = "";
          }
        
        });
      });
    });

    //add script to add questions
  });
  let tr = document.createElement("tr");
  const removePrompt = (prompt) => {
    
    socket.emit("remove_question", { prompt, user_id: userId });

  };
  if (userId != null) {
    socket.on("receive_prompts", function (questions) {
      prompts_wrapper.forEach((prompty) => {
        questions.forEach((question) => {
          if (question?.isList) {
            let data_html = `
            <tr>
            <td class="primary statusmail">
              <input type="checkbox" id="check2" class="checkbox" />
              <label for="check2"></label>
              <a href="javascript:void(0)">
                <div class="textmail">
                  <strong>${question?.prompt}</strong>
                  <!-- <span class="pull-right text-muted"> 
                    12 Jul 16 | 11:00 am
                  </span> -->
                  <p>
                   does have list : ${question?.isList}
                  </p>
                  <div class="question_options ${question?.prompt.replace(/ /g,"_").toLowerCase()}" data-prompt="${question?.prompt}"></div>
                  <button style="margin:8px;" class="btn btn-danger detele-prompt" data-prompt="${question?.prompt}">delete</button>
                </div>
              </a>
            </td>
          </tr>`;
          tr.innerHTML += data_html;
          }else{
            let data_html = `
            <tr>
            <td class="primary statusmail">
              <input type="checkbox" id="check2" class="checkbox" />
              <label for="check2"></label>
              <a href="javascript:void(0)">
                <div class="textmail">
                  <strong>${question?.prompt}</strong>
                  <!-- <span class="pull-right text-muted"> 
                    12 Jul 16 | 11:00 am
                  </span> -->
                  <p>
                   does have list : ${question?.isList}
                  </p>
                  
                  <button style="margin:8px;"  class="btn btn-danger detele-prompt" data-prompt="${question?.prompt}">delete</button>
                </div>
              </a>
            </td>
          </tr>`;
          tr.innerHTML += data_html;
          }
         
          if (question?.isList) {
            question?.options?.forEach((opt) => {
              document
                .querySelectorAll(`.${question?.prompt.replace(/ /g,"_").toLowerCase()}`)
                .forEach((container) => {
                  container.innerHTML += `
              <p class="btn btn-primary" style="height=10px">${opt.name}</p>

              `;
                });
            });
          }
        });
        if ($(".prompts_wrapper").html() !== tr) {
          $(".prompts_wrapper").html(tr);
        }
        $(".detele-prompt").on("click", () => {
          removePrompt($(".detele-prompt").attr("data-prompt"),$(this));
          $("td .detele-prompt").parent().remove();
        });
      });
    });
    socket.on("receive_question", function (question) {
      lastPrompt = question?.prompt;

      if (question?.isList) {
        //we loop the options they have and also question on top

        maillists.forEach((maillist, index) => {
          let data_html = `
            <a href="javascript:void(0)" class="mailpreview attachment">
            <div class="imgpic"><i class="icon"></i></div>
            <div class="textmail">
              <strong>${question?.prompt}</strong>
              <p class="question_options ${question?.prompt.replace(/ /g,"_").toLowerCase()}"></p>
              <span class="btn btn-default attachdownload"
                ></span>
            </div>
          </a>
            `;
          if (maillist.innerHTML != data_html) {
            maillist.innerHTML += data_html;
            if (question?.isList) {
              question?.options?.forEach((opt) => {
               
                document
                  .querySelectorAll(`.${question?.prompt.replace(/ /g,"_").toLowerCase()}`)
                  .forEach((container) => {
                    container.innerHTML += `
                <p class="btn btn-primary options" style="height=10px">${opt.name}</p>
  
                `;
                  });
                  document.querySelectorAll('.options').forEach((opt)=>{
                    opt.onclick = (e) =>{
                      question_id++;
                      userAnswer = { user_id: userId, answer: opt.innerHTML, question_id };
                      socket.emit("answer_question", userAnswer);
                      userAnswers.push({
                        prompt: lastPrompt,
                        answer: opt.innerHTML,
                        question_id,
                      });
                      maillists.forEach((maillist, index) => {
                        let data_html = `
                      <a href="javascript:void(0)" class="mailpreview attachment">
                      <div class="imgpic"><i class="-o"></i></div>
                      <div class="textmail">
                        <strong>${opt.innerHTML}</strong>
                        <p>answer</p>
                        <span class="btn btn-default attachdownload"
                          ></span>
                      </div>
                    </a>
                      `;
                        maillist.innerHTML += data_html;
                      });
                     
                    }
                  })
              });
            }
          }
        });
        genes.forEach((gene) => {
        
          let data_html = `
             <tr>
             <td class="primary statusmail">
               <input type="checkbox" id="check2" class="checkbox" />
               <label for="check2"></label>
               <a href="javascript:void(0)">
                 <div class="textmail">
                   <strong>${question?.prompt}</strong>
                   <!-- <span class="pull-right text-muted"> 
                     12 Jul 16 | 11:00 am
                   </span> -->
                   <p>
                    does have list : ${question?.isList}
                   </p>
                  
                 </div>
               </a>
             </td>
           </tr>`;

          if (gene.innerHTML != data_html) {
            gene.innerHTML += data_html;
          }
        });
      } else {
        maillists.forEach((maillist, index) => {
          
          let data_html = `
           <a href="javascript:void(0)" class="mailpreview attachment">
           <div class="imgpic"><i class="-o"></i></div>
           <div class="textmail">
             <strong>${question?.prompt}</strong>
             <p>question</p>
             <span class="btn btn-default attachdownload"
               ></span>
           </div>
         </a>
           `;
          maillist.innerHTML += data_html;
         
        });
        genes.forEach((gene) => {
         
          let data_html = `
            <tr>
            <td class="primary statusmail">
              <input type="checkbox" id="check2" class="checkbox" />
              <label for="check2"></label>
              <a href="javascript:void(0)">
                <div class="textmail">
                  <strong>${question?.prompt}</strong>
                  <!-- <span class="pull-right text-muted"> 
                    12 Jul 16 | 11:00 am
                  </span> -->
                  <p>
                   does have list : ${question?.isList}
                  </p>
                </div>
              </a>
            </td>
          </tr>`;
          gene.innerHTML += data_html;
        });
      }

      var item = document.createElement("li");
      item.textContent = question;
      messages.appendChild(item);
      window.scrollTo(0, document.body.scrollHeight);
    });
  }

  return () => {
    socket.off("receive_question");
    socket.off("receive_prompts");
    socket.off("me");
  };
});
