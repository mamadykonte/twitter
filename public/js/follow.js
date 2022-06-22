const onglets = Array.from(document.querySelectorAll(".onglets"));
const contenu = Array.from(document.querySelectorAll(".contenu"));


onglets.forEach(onglet => {
  onglet.addEventListener("click", tabsAnimation)
})

let index = 0;

function tabsAnimation(e){

    const el = e.target;
    
    onglets[index].classList.remove("active");
    contenu[index].classList.remove("active-contenu");
    
    index = onglets.indexOf(el);
    
    onglets[index].classList.add("active")
    contenu[index].classList.add("active-contenu");
    
}

const btnFollow = document.querySelectorAll(".userIs-Follow")
console.log(btnFollow);
if(btnFollow){
  btnFollow.forEach(btn => {
    console.log(btn);
    btn.addEventListener("mouseover", function(e) {
      btn.textContent = "Unfollow";
      btn.style.backgroundColor = "red";

    })
    btn.addEventListener("mouseout", function(e) {
      btn.textContent = "Following";
      btn.style.backgroundColor = "var(--primary)";
    }
    )
  })
}





