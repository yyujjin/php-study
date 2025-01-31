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
    getEvents();
  });
}

function changeDate(i) {
  //기존 값 저장
  eventHeader.innerHTML = originalContent + "-" + i;
  selectedDate["date"] = i;
}

function setDate() {
  let date = new Date();
  let year = date.getFullYear(); // 년도
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  let day = ("0" + date.getDate()).slice(-2);
  //let day = today.getDay(); // 요일

  return {
    year: year,
    month: month,
    date: day,
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

//날짜 클릭하면 그 날짜에 있는 일정 다 가져와서 배열만들기
async function getEvents() {
  const { year, month, date } = selectedDate;
  const response = await fetch(
    `event.php?createdDate=${year}-${month}-${date}`
  );
  const data = await response.json(); //JSON 데이터를 JavaScript 객체로 변환
  makeList(data);
}

//이벤트 가져와서 리스트 만들기
function makeList($events) {
  ul.innerHTML = "";
  $events.forEach((event) => {
    ul.innerHTML += `<li>${event.events}</li>`;
  });
}

function addEventToList(event) {
  ul.innerHTML += `<li>${event}</li>`;
  input.value = "";
}

//전역으로 빼서
//그걸 값을 함수로 넣어서 값을 바꾸고
//추가하면 그 값에다가
