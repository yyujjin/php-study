const eventColumn = document.querySelector(".event-column");
const eventHeader = document.querySelector(".event-header");
const originalContent = eventHeader.innerHTML; //-년-월 저장해두기
const day = document.querySelectorAll(".day");
const input = document.querySelector("input");
const ul = document.querySelector("ul");
const button = document.querySelector("button");

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

//일정 추가
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
      input.value = "";
      //addEventToList(input.value)
      getEvents();
    }
  } catch (error) {
    alert("잠시 후 다시 시도하세요");
  }
}

//일정 추가 후 리스트 하위에 추가
//hmlt 상에서만 나타나서 그냥 추가하면 다시 api 요청해서 데이터 불러와서 처리함
//안쓰고 있음 나중에 삭제하기
function addEventToList(event) {
  ul.innerHTML += `<li>${event}</li>`;
  input.value = "";
}

//일정 조회
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
    ul.innerHTML += `<li id="${event.id}">${event.events}</li>`;
  });
  editEvent();
}

//나중에 Hover 사용하기
//edit 버튼 클릭하면 일정 수정할 수 있게 하기
//add 버튼 아무것도 입력하지 않으면 경고창 날리기
function editEvent() {
  //   const editButtons = document.querySelectorAll(".editButton");
  //   editButtons.forEach((editButton) => {
  //     editButton.addEventListener("click", async function () {
  //       try {
  //         const response = await fetch("event.php", {
  //           method: "POST",
  //           body: JSON.stringify({
  //             id: editButton.id,
  //             event: input.value,
  //           }),
  //         });
  //         const textData = await response.text();
  //         if (textData == "success") {
  //           alert("일정이 수정되었습니다." + input.value);
  //           getEvents();
  //         }
  //       } catch (error) {
  //         alert("잠시 후 다시 시도하세요");
  //       }
  //     });
  //   });
}
