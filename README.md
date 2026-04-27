# Real-timer-web
# On-device AI 기반 학습 트래킹 시스템

> **AWS 3-Tier 아키텍처와 TensorFlow.js를 결합한 개인정보 보호형 학습 시간 관리 프로젝트**

## 1. 프로젝트 개요
- **배경:** 서버 사이드 AI 연산의 고비용 문제와 영상 데이터 전송에 따른 프라이버시 리스크 해결
- **핵심 목표:** 엣지 컴퓨팅을 통한 인프라 비용 절감 및 데이터 보안 강화

## 2. 기술 스택 (Tech Stack)
- **Infrastructure:** AWS (ALB, EC2, RDS, ACM, Route 53)
- **OS/Runtime:** Amazon Linux 2023, Apache 2.4, PHP 8.2
- **Database:** MariaDB (RDS)
- **AI Engine:** TensorFlow.js, PoseNet
- **Frontend:** HTML5, CSS3, Vanilla JS (Fetch API)

## 3. 시스템 아키텍처 (Architecture)

- **Security:** ALB를 통한 SSL/TLS Termination 처리 및 전구간 HTTPS 적용
- **Network:** Database를 Private Subnet에 배치하여 외부 접근 원천 차단
- **Scalability:** AWS 리소스를 활용한 3-Tier 계층 분리 설계

## 4. 핵심 로직 (Core Logic)
### On-device Pose Detection
- 브라우저에서 직접 PoseNet을 구동하여 사용자의 'Nose' 키포인트 추출
- **Confidence Score 필터링:** - `Score > 0.6`: Active (학습 중)
  - `Score < 0.2`: Idle (자리 비움)

### 1s Heartbeat Protocol
- 1초 주기 비동기(AJAX) 통신을 통해 실시간 데이터 동기화
- 영상 데이터가 아닌 텍스트 기반 메타데이터(JSON)만 전송하여 네트워크 부하 최소화

## 5. 성과 및 결과 (Key Results)
- **비용 절감:** 서버 사이드 GPU 연산 배제로 인프라 유지 비용 약 90% 이상 절감
- **보안성:** 영상 데이터를 서버로 전송하지 않아 프라이버시 침해 우려 완전 해소
- **최적화:** AL2023 커널 환경에서 PHP-FPM 튜닝을 통한 요청 처리 성능 향상

---
**Contact:** chlee0416@naver.com  
**Portfolio:** [포트폴리오 주소]
