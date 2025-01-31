const eventColumn = document.querySelector(".event-column");
const eventHeader = document.querySelector(".event-header");
const originalContent = eventHeader.innerHTML; //-년-월 저장해두기
const day = document.querySelectorAll(".day");
const input = document.querySelector("input");
const ul = document.querySelector("ul");

let selectedDate = setDate();

//처음은 당일로
eventHeader.innerHTML = `${selectedDate.year}-${selectedDate.month}-${selectedDate.date}`;

for (let i = 0; i < day.length; i++) {
  day[i].addEventListener("click", function () {
    changeDate(i + 1);
    ul.innerHTML = "";
  });
}

function changeDate(i) {
  //기존 값 저장
  eventHeader.innerHTML = originalContent + "-" + i;
  selectedDate["date"] = i;
  console.log(selectedDate);
}

function setDate() {
  let today = new Date();
  let year = today.getFullYear(); // 년도
  let month = today.getMonth() + 1; // 월
  let date = today.getDate(); // 날짜
  //let day = today.getDay(); // 요일

  return {
    year: year,
    month: month,
    date: date,
  };
}

async function addEvent() {
  const { year, month, date } = selectedDate;

  try {
    const response = await fetch("event.php", {
      method: "POST",
      body: JSON.stringify({
        event: input.value,
        createdDate: `${year}-${month}-${date}`,
      }),
    });
    const textData = await response.text();

    if (textData == "success") {
      alert("일정이 추가되었습니다." + input.value);
      addEventToList(input.value);
    }
  } catch (error) {
    alert("잠시 후 다시 시도하세요");
  }
}

function addEventToList(event) {
  //ul.innerHTML = "";
  ul.innerHTML += `<li>${event}</li>`;
}
